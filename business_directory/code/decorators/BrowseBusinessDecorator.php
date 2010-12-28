<?php

class BrowseBusinessDecorator extends DataObjectDecorator {

	static $max_radius = 100;

	function __construct () {
		parent::__construct();
	}

	function setSidebarImage() {
		return false;
	}

	function getParentIDs() {
		$array = array();
		$array[$this->owner->ID] = $this->owner->ID;
		if($this->owner->ParentID) {
			$parent = DataObject::get_by_id("SiteTree", $this->owner->ParentID);
			$parentArray = $parent->getParentIDs();
			if(count($parentArray)) {
				$array = array_merge($array, $parentArray);
			}
		}
		return $array;
	}

	function BusinessList() {
		$array = $this->getParentIDs();
		return DataObject::get("BusinessPage", "ParentID IN (".implode(",", $array).") AND ShowInMenus = 1");
	}

}

class BrowseBusinessDecorator_Controller extends Extension {

	static $allowed_actions = array(
		"createnewbusinesslistingfrompoint" => true
	);

	function index() {
		$this->owner->addCustomMap($this->owner->Children()); //DataObject::get("BusinessPage")
		return Array();
	}

}


