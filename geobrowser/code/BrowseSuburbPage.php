<?php

class BrowseSuburbPage extends BrowseAbstractPage {

	static $icon = "geobrowser/images/treeicons/BrowseSuburbPage";

	static $default_parent = "BrowseCitiesPage";

	static $can_be_root = false;

	static $has_one = array(
		"City" => "BrowseCitiesPage"
	);

	public function canCreate() {
		return parent::canCreate();
	}


	public function allowBrowseChildren() {
		return true;
	}


	public function GeoLevelName() {
		return "Suburbs";
	}

	public function GeoLevelNumber() {
		return 4;
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}



}

class BrowseSuburbPage_Controller extends BrowseAbstractPage_Controller {

	function init() {
		parent::init();
	}


}

