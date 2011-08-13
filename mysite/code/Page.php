<?php

class Page extends SiteTree {

	static $has_one = array(
		"Sidebar" => "WidgetArea",
		"BackgroundImage" => "Image"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("Sidebar"));
		return $fields;
	}


	function requireDefaultRecords() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		parent::requireDefaultRecords();
		$page = DataObject::get_one("Page", "{$bt}URLSegment{$bt} = 'admin-only'");
		if(!$page) {
			$page = new Page();
			$page->URLSegment = "admin-only";
			$page->Title = "Admin Only";
			$page->ShowInMenus = 0;
			$page->ShowInSearch = 0;
			$page->writeToStage("Stage");
			$page->publish("Stage", "Live");
		}
	}


	function MyBackgroundImage() {
		if($this->BackgroundImageID) {
			if($image = $this->BackgroundImage()) {
				return $image;
			}
		}
		if($this->ParentID) {
			if($parent = DataObject::get_by_id("SiteTree", "ParentID = ".$this->ParentID)) {
				return $parent->MyBackgroundImage();
			}
		}
		if($siteConfig = SiteConfig::current_site_config()) {
			return $siteConfig->BackgroundImage();
		}
	}

}

class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		$this->addBasicMetatagRequirements();
		if($bgImage = $this->MyBackgroundImage()) {
			Requirements::customCSS("body {background-image: url(".$bgImage->Link().");}");
		}		
	}

}

