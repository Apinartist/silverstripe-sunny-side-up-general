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

	public static $indexes = array(
		"Multiplier" => true
	);

	public static $defaults = array(
		"Multiplier" => 1
	);

	public static $default_records = array(
		array("Name" => "Normal", "Multiplier" => 1),
		array("Name" => "Urgent", "Multiplier" => 1.7)
		array("Name" => "Rush", "Multiplier" => 2.7)
	);

	public static $default_sort = "Multiplier";

	public static $summary_fields = array(
		"Name",
		"Multiplier"
	);

	public static $singular_name = "Timeframe Option";

	public static $plural_name = "Timeframe Options";

}