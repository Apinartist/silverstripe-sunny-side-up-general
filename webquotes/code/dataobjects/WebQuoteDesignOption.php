<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteTimeframeOption extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"Multiplier" => "Double(4,4)"
	);

	public static $casting = array();

	public static $indexes = array(
		"Multiplier" => true
	);

	public static $defaults = array(
		"Multiplier" => 1
	);

	public static $default_records = array(
		array("Name" => "Clean and Professional", "Multiplier" => 1),
		array("Name" => "Basic", "Multiplier" => 0.7),
		array("Name" => "Sophisticated and Complex", "Multiplier" => 1.3)
	);

	public static $has_one = null;

	public static $has_many = null;

	public static $many_many = null;

	public static $many_many_extraFields = null;

	public static $belongs_many_many = null;

	public static $default_sort = "Multiplier";

	public static $searchable_fields = array(
	);

	public static $field_labels = array(
	);

	public static $summary_fields = array(
		"Name",
		"Multiplier"
	);

	public static $singular_name = "Design Option";

	public static $plural_name = "Design Options";

}