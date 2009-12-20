<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: list of Share This Options that can be shown
 *
 **/

class ShareThisDataObject extends DataObject {


	public static $db = array(
		'Title' => 'Varchar(20)',
		'Show' => 'Boolean'
	);

	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array();

	public static $searchable_fields = array();

	public static $field_labels = array(
		"Title" => "Name",
		"Show" => "Show"
	);

	public function canView() {
		return false;
	}

	public function canDelete() {
		return false;
	}

	public static $summary_fields = array(
		"Title" => "Name",
		"Show" => "Show"
	);

	public static $singular_name = "Share This Option to Show";

	public static $plural_name = "Share This Options to Show";

	function onAfterWrite() {
		parent::onAfterWrite();
		if($obj = DataObject::get("ShareThisDataObject", "`Title` = '".$this->Title."' AND ID <> ".$this->ID)) {
			$obj->delete();
		}
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$page = DataObject::get_one("SiteTree", "ShareIcons = 1");
		$keys = $page->ShareIconsKeys();
		foreach($keys as $key) {
			$o = new ShareThisDataObject();
			$o->Title = $key;
			$o->Show = true;
			$o->write();
		}
	}

}