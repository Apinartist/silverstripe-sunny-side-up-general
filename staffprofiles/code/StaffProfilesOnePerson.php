<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: displays a list of staff profiles
 *
 */
class StaffProfilesOnePerson extends Page {

	public static $icon = "mysite/images/treeicons/StaffProfilesOnePerson";
	//parents and children in sitetree

	static $allowed_children = "none"; //can also be "none";

	static $default_parent = "StaffProfilesPage"; // *URLSegment* of default parent node.

	static $can_be_root = false; //default is true
	//parents and children in classes

	static $db = array(
		"Email" => "Varchar(100)",
		"Title" => "Varchar(100)"
	);

	static $has_one = array(
		"Photo" => "Image"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.PersonalDetails", new TextField("Email", "Email"));
		$fields->addFieldToTab("Root.Content.PersonalDetails", new TextField("Title", "Title"));
		$fields->addFieldToTab("Root.Content.PersonalDetails", new ImageField("Photo", "Photo"));
		return $fields;
	}

}

class StaffProfilesOnePerson_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::themedCSS("StaffProfilesOnePerson");
	}

}

