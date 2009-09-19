<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteSalesGroup extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"Description" => "Text",
		"SortNumber" => "Int"
	);

	public static $casting = array();

	public static $indexes = array();

	public static $defaults = array();

	public static $default_records = array(
		array("Name" => "Clean and Professional", "Multiplier" => 1),
		array("Name" => "Basic", "Multiplier" => 0.7),
		array("Name" => "Sophisticated and Complex", "Multiplier" => 1.3)
	);

	public static $has_one = null;

	public static $has_many = array(
		"SalesItem" => "WebQuoteSalesItem"
	);

	public static $many_many = array(

	);

	public static $many_many_extraFields = null;

	public static $belongs_many_many = null;

	public static $default_sort = "SortNumber";

	public static $searchable_fields = array(
	);

	public static $field_labels = array(
	);

	public static $summary_fields = array(
		"Name",
		"SortNumber"
	);

	public static $singular_name = "Sales Group";

	public static $plural_name = "Sales Groups";

}