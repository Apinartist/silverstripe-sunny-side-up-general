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
		'IncludeThisIcon' => 'Boolean',
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
		"IncludeThisIcon" => "Include this icon",
		"Sort" => "Sort Index (lower numbers shown first)"
	);

	public static $default_sort = "IncludeThisIcon DESC, Sort ASC, Title ASC";

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
		"IncludeThisIcon" => "IncludeThisIcon"
	);

	public static $singular_name = "Share this page icon";

	public static $plural_name = "Share this page icons";

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
			$actualArray = ShareThisOptions::get_general_data();
			ShareThis::set_icons_to_include(array());
			ShareThis::set_icons_to_exclude(array());
			ShareThisOptions::set_general_data(null);
			$fullArray = ShareThisOptions::get_general_data();
			foreach($fullArray as $key) {
				if(!DataObject::get("ShareThisDataObject", "Title = '".$key."'")) {
					$o = new ShareThisDataObject();
					$o->Title = $key;
					$style = "excluded";
					$o->IncludeThisIcon = 0;
					if(in_array($key, $actualArray)) {
						$o->IncludeThisIcon = 1;
						$style = "included";
					}
					$o->write();
					DB::alteration_message("Added Bookmark Icon for ".$key." (".$style.")", "created");
				}
			}
		}
		$inc = ShareThis::get_icons_to_include();
		$exc = ShareThis::get_icons_to_exclude();
		if(count($inc)) {
			foreach($inc as $key) {
				if($obj = DataObject::get("ShareThisDataObject", "Title = '".$key."' AND IncludeThisIcon = 0")) {
					$obj->IncludeThisIcon = 1;
					$obj->write();
					DB::alteration_message("updated inclusion for ".$key, "created");
				}
			}
		}
		if(count($exc)) {
			foreach($exc as $key) {
				if($obj = DataObject::get("ShareThisDataObject", "Title = '".$key."' AND IncludeThisIcon = 1")) {
					$obj->IncludeThisIcon = 0;
					$obj->write();
					DB::alteration_message("updated inclusion for ".$key, "created");
				}
			}
		}
	}

}
