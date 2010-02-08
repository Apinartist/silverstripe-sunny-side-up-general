<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description
 *
 **/

class StandingOrder extends DataObject {

	protected static $frequency_options = array("Daily", "Weekly", "Fortnightly", "Monthly");
		static function set_frequency_options(Array $array) {self::$frequency_options = $array;}
		static function get_frequency_options() {return self::$frequency_options = $array;}

	protected static $possible_order_days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
		static function set_possible_order_days(Array $array) {self::$possible_order_days = $array;}
		static function get_possible_order_days() {return self::$possible_order_days = $array;}

	//database
	public static $db = array(
		"StartDate" => "Date",
		"EndDate" => "Date",
		"Frequency" => "Int"
	);

	public static $has_one = array(
		"Member" => "Member"
	);

	public static $has_many = array(
		"StandingOrderProducts" => "StandingOrder_Product",
		"StandingOrderEntries" => "StandingOrder_Entry"
	);

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $many_many_extraFields = array();
	//database related settings

	static $indexes = array(
		"SearchFields" => "fulltext (Title, MenuTitle, Content, MetaTitle, MetaDescription, MetaKeywords)",
		"TitleSearchFields" => "fulltext (Title)",
		"Sort" => true,
		"Uniques" => "Unique('URLSegment')",
	);

	//formatting

	public static $casting = array(); //adds computed fields that can also have a type (e.g.

	public static $searchable_fields = array(
		"Member.Title" => "PartialMatchFilter"
		"StartDate",
		"EndDate",
	);

	public static $field_labels = array("Name" => "Carrot Name");

	public static $summary_fields = array("Name" => "Name");

	public static $singular_name = "Carrot";

	public static $plural_name = "Carrots";

	//CRUD settings

	static $can_create = Boolean;

	public function canCreate() {return false;}

	public function canView() {return false;}

	public function canEdit() {return false;}

	public function canDelete() {return false;}

	//defaults
	public static $default_sort = "Sort ASC, Name ASC";
	public static $defaults = array();//use fieldName => Default Value
	public function populateDefaults() {
		parent::populateDefaults();
	}
}

















}