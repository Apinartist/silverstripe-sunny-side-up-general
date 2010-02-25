<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: displays a list of staff profiles
 *
 */
class StaffProfilesPage extends Page {

	public static $icon = "mysite/images/treeicons/StaffProfilesPage";

	public static $has_many = array(
		"StaffProfiles" => "StaffProfile"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab(
			"Root.Content.Profiles",
			new HasManyComplexTableField(
				$controller = $this,
				$name = "StaffProfiles",
				$sourceClass = "StaffProfile",
				StaffProfile::$summary_fields,
				$detailFormFields = null,
				$sourceFilter = "ParentID = ".$this->ID
			)
		);
		return $fields;
	}

}

class StaffProfilesPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::themedCSS("StaffProfilesPage");
	}

}

