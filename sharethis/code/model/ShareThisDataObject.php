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
		'Show' => 'Boolean',
		"Sort" => "Int"
	);

	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array();

	public static $searchable_fields = array();

	public static $field_labels = array(
		"Title" => "Name",
		"Show" => "Show",
		"Sort" => "Sort Index (lower numbers shown first)"
	);

	public static $default_sort = "Show DESC, Sort ASC, Title ASC";

	public function canView($member = false) {
		return Permission::check('CMS_ACCESS_CMSMain');
	}

	public function canDelete() {
		return true;
	}

	public function canEdit($member = false) {
		return $this->canView();
	}

	public static $summary_fields = array(
		"Title" => "Name",
		"Show" => "Show"
	);

	public static $singular_name = "Link to promote page";

	public static $plural_name = "Links to promote page";

	function onAfterWrite() {
		parent::onAfterWrite();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($obj = DataObject::get("ShareThisDataObject", "{$bt}Title{$bt} = '".$this->Title."' AND ID <> ".$this->ID)) {
			$obj->delete();
		}
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(ShareThis::get_use_data_object()) {
			//must be after the line below
			ShareThis::set_use_data_object(0);
			ShareThis::set_always_include(1);
			ShareThis::set_icons_to_include(array());
			ShareThis::set_icons_to_exclude(array());
			$page = DataObject::get_one("SiteTree");
			if($page) {
				if(method_exists($page, "ShareIconsKeys")) {
					$keys = $page->ShareIconsKeys();
					if(count($keys)) {
						foreach($keys as $key) {
							if(!DataObject::get("ShareThisDataObject", "Title = '".$key."'")) {
								$o = new ShareThisDataObject();
								$o->Title = $key;
								$o->Show = 1;
								$o->write();
							}
						}
					}
					else {
						debug::show("there are no social bookmark icons listed");
					}
				}
			}
		}
	}

}
