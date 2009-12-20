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
		ShareThis::set_use_data_object(0);
		ShareThis::set_always_include(1);
		ShareThis::set_icons_to_include(array());
		ShareThis::set_icons_to_exclude(array());
		if(ShareThis::get_use_data_object()) {
			$page = DataObject::get_one("SiteTree");
			if($page) {
				$keys = $page->ShareIconsKeys();
				foreach($keys as $key) {
					if(!DataObject::get("ShareThisDataObject", "Title = '".$key."'")) {
						$o = new ShareThisDataObject();
						$o->Title = $key;
						$o->Show = 1;
						$o->write();
					}
				}
			}
		}
	}

}