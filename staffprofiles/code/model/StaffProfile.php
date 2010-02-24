<?php

/**
 *@author: nicolaas[at]sunnysideup.co.nz
 *@description: individual staff profile
 *
 **/

class StaffProfile extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"Title" => "Varchar(255)",
		"Description" => "Text",
		"Email" => "Varchar(255)",
		"Sort" => "Int"
	);

	public static $has_one = array(
		"ProfilePicture" => "Image",
		"Parent" => "StaffProfilesPage"
	);

	//database related settings
	static $indexes = array(
		"Sort" => true
	);

	//formatting
	public static $searchable_fields = array("Name" => "PartialMatchFilter");

	public static $field_labels = array("Sort" => "Sort Index Number for Sorting (lower numbers first)");

	public static $summary_fields = array("Name" => "Name", "Email" => "Email", "Title" => "Title");

	public static $singular_name = "Staff Profile";

	public static $plural_name = "Staff Profiles";

	public static $default_sort = "Sort ASC, Name ASC";

	public static $defaults = array(
		"Sort" => 100
	);

	public function populateDefaults() {
		parent::populateDefaults();
		$this->Sort = 100;
	}

  public function EmailObfuscator() {
		if($email = $this->Email) {
			$array = split("@", $this->Email);
			if(is_array($array)) {
				if(count($array) == 2) {
					return $array[0]." [at] ".$array[1];
				}
			}
		}
	}

}