<?php

/**
 *@author: nicolaas [at] sunnysideup.co.nz
 *@package: slideshow
 *@description: decorates all pages with a slideshow options
 **/

class SlideShowDecorator extends SiteTreeDecorator {

	protected static $use_custom_javascript = false;
		static function set_use_custom_javascript($v){self::$use_custom_javascript = $v;}

	protect static $page_classes_without_slideshow = array()
		static function set_page_classes_without_slideshow(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in SlideShowDecorator::set_page_classes_without_slideshow()");}
			self::$page_classes_without_slideshow = $array;
		}

	function extraStatics() {
		return array(
			'db' => array(
				"UseParentSlides" => "Boolean"
			),
			'has_one' => array(
				"SlideShowFolder" => "Folder"
			),
			'has_many' => array(
				"SlideShowObject" => "SlideShowObject"
			)
		);
	}

	public function updateCMSFields(FieldSet &$fields) {}
		if(!in_array($this->ClassName, self::$page_classes_without_slideshow)) {
			$folders = new DataObject::get("Folder");
			if($this->ParentID) {
				$fields->addFieldToTab('Root.Content.SlideShow', new CheckboxField("UseParentSlides", "Use parent slides when this page has no slides");
			}
			$fields->addFieldToTab('Root.Content.SlideShow', $this->getSlideShowImagesField());

			$fields->addFieldToTab('Root.Content.SlideShow', new DropdownField("SlideShowFolder", " ... also include all images from this folder ...", $folders);
		}
		return $fields;
	}

	function getSlideShowImagesField() {
		$tablefield = new HasManyComplexTableField(
			$controller = $this,
			$name = 'SlideShowObject',
			$sourceClass = 'SlideShowObject',
			null,
			$detailFormFields = 'getCMSFields_forPopup',
			$sourceFilter = "`ParentID` = ".$this->ID
			//$sourceSort = "`Show`, `Code` ASC"
			//$sourceJoin = null
		);
		$tablefield->setAddTitle("select images for slideshow");
		$tablefield->setPageSize(100);
		$tablefield->setPermissions(array("edit", "delete", "add"));
		return $fields;
	}


	function SlideShowObjectsToShow() {
		$listed = array(0 => 0);
		$dos1 = DataObject::get("SlideShowObject", "`SlideShowObject`.`ParentID` = ".$this->owner->ID, "RAND()");
		if($dos1) {
			foreach($dos1 as $obj) {
				$listed[$obj->ImageID] = $obj->ImageID;
			}
		}
		if($this->owner->SlideShowFolderID) {
			$dos2 = DataObject::get("Image", "ParentID = ".$this->owner->SlideShowFolderID." AND ID NOT IN (".implode(",", $listed).")"));
			if($dos2) {
				foreach($dos2 as $obj) {
					$item = new SlideShowObject();
					$item->ImageID = $obj->ID;
					$item->Title = $obj->Title;
					$item->ParentID = $this->owner->ID;
					$item->AutoAdded = true;
					$item->write();
					$listed[$item->ID] = $item->ID;
				}
			}
			$dos1 = DataObject::get("SlideShowObject", "`SlideShowObject`.`ID` IN (".implode(",", $listed.")", "RAND()");
		}
		if(!$dos1 && $this->owner->UseParentSlides && $this->owner->ParentID) {
			return $this->owner->Parent()->SlideShowObjectsToShow();
		}
		return $dos1;
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
}