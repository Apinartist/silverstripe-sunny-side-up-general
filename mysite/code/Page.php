<?php

class Page extends SiteTree {

	static $has_one = array(
		"Sidebar" => "WidgetArea",
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

}

class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		$this->addBasicMetatagRequirements();
	}

}

