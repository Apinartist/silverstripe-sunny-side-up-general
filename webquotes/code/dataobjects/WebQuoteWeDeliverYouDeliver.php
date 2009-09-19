<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteWeDeliverYouDeliver extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"WeDeliver" => "Text",
		"YouDeliver" => "Text",
		"BestSuitedTo" => "Text",
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
		array(
			"Name" => "Private Company",
			"WeDeliver" => "All content (text and photos)",
			"YouDeliver" => "Complete website implementation"
			"BestSuitedTo" => "Best option for people/organisations with their own in-house designer(s). A reasonable level of web-experiece is required."
			"Multiplier" => 1
		),
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

	public static $singular_name = "Delivery Option";

	public static $plural_name = "Deliver Options";

}