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
		static function set_page_classes_without_slideshow(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in SlideShowDecorator::set_page_classes_without_slideshow()");}
			self::$page_classes_without_slideshow = $array;
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
		if(!count(self::$page_classes_without_slideshow) || !in_array($this->owner->ClassName, self::$page_classes_without_slideshow)) {
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
		$tablefield = new HasManyComplexTableField(
			$controller = $this->owner,
			$name = 'SlideShowObject',
			$sourceClass = 'SlideShowObject',
			$fieldList =  array("Link" => "Link", "Title" => "Title"),
			$detailFormFields = 'getCMSFields_forPopup',
			$sourceFilter = "`ParentID` = ".$this->owner->ID
			//$sourceSort = "`Show`, `Code` ASC"
			//$sourceJoin = null
		);
		$tablefield->setAddTitle("select images for slideshow");
		$tablefield->setPageSize(100);
		$tablefield->setPermissions(array("edit", "delete", "add"));
		return $tablefield;
	}


	function SlideShowObjectsToShow() {
		if($this->owner->SlideShowIDList) {
			return DataObject::get("SlideShowObject", '`ID` IN ('.$this->owner->SlideShowIDList.')');
		}
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
			Requirements::javascript($folder."/javascript/SlideShow.js");
			Requirements::themedCSS("SlideShow");
		}
		return $dos;
	}

	public function onBeforeWrite() {
		if($parentID = $this->owner->ID) {
			$objects = array(0 => 0);
			$images = array(0 => 0);
			$dos1 = DataObject::get("SlideShowObject", "`SlideShowObject`.`ParentID` = ".$parentID, "RAND()");
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
				$dos2 = DataObject::get("Image", "ParentID = ".$this->owner->SlideShowFolderID." AND ID NOT IN (".implode(",", $images).")");
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
				$dos1 = DataObject::get("SlideShowObject", "`SlideShowObject`.`ID` IN (".implode(",", $objects).")", "RAND()");
			}
			if(!$dos1 && $this->owner->UseParentSlides) {
				$parent = DataObject::get_by_id("SiteTree", $parentID);
				if($parent) {
					$this->owner->SlideShowIDList = $parent->SlideShowIDList;
				}
				elseif($this->URLSegment != "home") {
					$home = DataObject::get_one("SiteTree", '`URLSegment` = "home"');
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