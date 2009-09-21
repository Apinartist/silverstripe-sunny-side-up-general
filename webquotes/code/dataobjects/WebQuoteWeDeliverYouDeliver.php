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
			"YouDeliver" => "Complete website implementation",
			"BestSuitedTo" => "Best option for people/organisations with their own in-house designer(s). A reasonable level of web-experiece is required.",
			"Multiplier" => 1
		),
	);

	public static $default_sort = "Multiplier";

	public static $summary_fields = array(
		"Name",
		"Multiplier"
	);

	public static $singular_name = "Who does what option";

	public static $plural_name = "Who does what options";

}