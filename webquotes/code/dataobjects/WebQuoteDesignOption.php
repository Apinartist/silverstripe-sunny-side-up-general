<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteDesignOption extends DataObject {

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
		array("Name" => "Clean and Professional", "Multiplier" => 1),
		array("Name" => "Basic", "Multiplier" => 0.7),
		array("Name" => "Sophisticated and Complex", "Multiplier" => 1.3)
	);

	public static $default_sort = "Multiplier";

	public static $summary_fields = array(
		"Name",
		"Multiplier"
	);

	public static $singular_name = "Design Option";

	public static $plural_name = "Design Options";

}