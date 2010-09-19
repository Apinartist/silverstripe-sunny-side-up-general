<?php

/**
 *@author: nicolaas [at] sunnysideup.co.nz
 *@package: slideshow
 *@description: decorates all pages with a slideshow options
 **/

class SlideShowDecorator extends SiteTreeDecorator {

	protected static $use_custom_javascript = false;
		static function set_use_custom_javascript($v){self::$use_custom_javascript = $v;}

	protected static $page_classes_without_slideshow = array();
		static function get_page_classes_without_slideshow(){return self::$page_classes_without_slideshow;}
		static function set_page_classes_without_slideshow(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in SlideShowDecorator::set_page_classes_without_slideshow()");}
			self::$page_classes_without_slideshow = $array;
		}

	protected static $page_classes_with_slideshow = array();
		static function get_page_classes_with_slideshow(){return self::$page_classes_with_slideshow;}
		static function set_page_classes_with_slideshow(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in SlideShowDecorator::set_page_classes_with_slideshow()");}
			self::$page_classes_with_slideshow = $array;
		}

	function extraStatics() {
		return array(
			'db' => array(
				"UseParentSlides" => "Boolean",
				"SlideShowIDList" => "Text"
			),
			'has_one' => array(
				"SlideShowFolder" => "Folder"
			),
			'has_many' => array(
				"SlideShowObject" => "SlideShowObject"
			)
		);
	}

	public function updateCMSFields(FieldSet &$fields) {
		if($this->classHasSlideShow($this->owner->ClassName)) {
			$folders = DataObject::get("Folder");
			if($this->owner->ParentID) {
				$fields->addFieldToTab('Root.Content.SlideShow', new CheckboxField("UseParentSlides", "Use parent slides when this page has no slides itself"));
			}
			elseif($this->owner->URLSegment != "home") {
				$fields->addFieldToTab('Root.Content.SlideShow', new CheckboxField("UseParentSlides", "Use homepage slides IF this page has no slides and the homepage does"));
			}
			$fields->addFieldToTab('Root.Content.SlideShow', $this->SlideShowTableField());
			$fields->addFieldToTab('Root.Content.SlideShow', new TextField("SlideShowIDList"));
			$folder = new TreeDropdownField( 'SlideShowFolderID', 'Show ALL images From - you can leave this blank', 'Folder' );
			$folder->setFilterFunction( create_function( '$obj', 'return $obj->class == "Folder";' ) );
			$fields->addFieldToTab('Root.Content.SlideShow', $folder);
		}
		return $fields;
	}

	function SlideShowTableField() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$tablefield = new HasManyComplexTableField(
			$controller = $this->owner,
			$name = 'SlideShowObject',
			$sourceClass = 'SlideShowObject',
			$fieldList =  array("Link" => "Link", "Title" => "Title"),
			$detailFormFields = 'getCMSFields_forPopup',
			$sourceFilter = "{$bt}ParentID{$bt} = ".$this->owner->ID
			//$sourceSort = "{$bt}Show{$bt}, {$bt}Code{$bt} ASC"
			//$sourceJoin = null
		);
		$tablefield->setAddTitle("select images for slideshow");
		$tablefield->setPageSize(100);
		$tablefield->setPermissions(array("edit", "delete", "add"));
		return $tablefield;
	}


	function SlideShowObjectsToShow() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($this->owner->SlideShowIDList) {
			return DataObject::get("SlideShowObject", "{$bt}ID{$bt} IN (".$this->owner->SlideShowIDList.")");
		}
	}

	protected function classHasSlideShow($className) {
		//assumptions:
		//1. in general YES
		//2. if list of WITH is shown then it must be in that
		//3. otherwise check if it is specifically excluded (WITHOUT)
		$result = true;
		if(is_array(self::get_page_classes_with_slideshow()) && count(self::get_page_classes_with_slideshow())) {
			$result = false;
			if(in_array($className,self::get_page_classes_with_slideshow())) {
				$result = true;
			}
		}
		elseif(is_array(self::get_page_classes_without_slideshow()) && in_array($className,self::get_page_classes_without_slideshow()))  {
			$result = false;
		}
		return $result;
	}

	function SlidesForShow() {
		$dos = $this->SlideShowObjectsToShow();
		if($dos) {
			if(self::$use_custom_javascript) {
				$folder = project();
			}
			else {
				$folder = "slideshow";
			}
			Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
			Requirements::javascript($folder."/javascript/SlideShow.js");
			Requirements::themedCSS("SlideShow");
		}
		return $dos;
	}

	public function onBeforeWrite() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($parentID = $this->owner->ID) {
			$objects = array(0 => 0);
			$images = array(0 => 0);
			$dos1 = DataObject::get("SlideShowObject", "{$bt}SlideShowObject{$bt}.{$bt}ParentID{$bt} = ".$parentID, "RAND()");
			if($dos1) {
				foreach($dos1 as $obj) {
					$images[$obj->ID] = $obj->ImageID;
					$objects[$obj->ID] = $obj->ID;
				}
			}
			foreach($images as $objectID => $imageID) {
				if(!DataObject::get_by_id("Image", $imageID)) {
					$obj = DataObject::get_by_id("SlideShowObject", $objectID);
					if($obj) {
						$obj->delete();
						unset($objects[$objectID]);
					}
				}
			}
			if($this->owner->SlideShowFolderID) {
				$dos2 = DataObject::get("Image", "{$bt}ParentID{$bt} = ".$this->owner->SlideShowFolderID." AND {$bt}ID{$bt} NOT IN (".implode(",", $images).")");
				if($dos2) {
					foreach($dos2 as $obj) {
						$item = new SlideShowObject();
						$item->ImageID = $obj->ID;
						$item->Title = $obj->Title;
						$item->ParentID = $parentID;
						$item->AutoAdded = true;
						$item->write();
						$objects[$item->ID] = $item->ID;
					}
				}
				$dos1 = DataObject::get("SlideShowObject", "{$bt}SlideShowObject{$bt}.{$bt}ID{$bt} IN (".implode(",", $objects).")", "RAND()");
			}
			if(!$dos1 && $this->owner->UseParentSlides) {
				$parent = DataObject::get_by_id("SiteTree", $parentID);
				if($parent) {
					$this->owner->SlideShowIDList = $parent->SlideShowIDList;
				}
				elseif($this->URLSegment != "home") {
					$home = DataObject::get_one("SiteTree", "{$bt}URLSegment{$bt} = 'home'");
					if($home) {
						$this->owner->SlideShowIDList = $home->SlideShowIDList;
					}
				}
			}
			elseif($dos1) {
				$this->owner->SlideShowIDList = implode(",",$dos1->column("ID"));
			}
		}
		parent::onBeforeWrite();
	}


}
