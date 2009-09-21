<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteCurrencyConversion extends DataObject {

	public static $db = array(
		"Code" => "Varchar(3)",
		"Name" => "Varchar(255)",
		"DefaultValue" => "Double(4,4)",
		"Multiplier" => "Double(4,4)"
	);

	public static $indexes = array(
		"Code" => true
	);

	public static $defaults = array(
		"DefaultValue" => 1,
		"Multiplier" => 1
	);

	public static $default_records = array(
		array("Code" => "nzd", "Name" => "New Zealand Dollar", "DefaultValue" => 1)
	);

	public static $default_sort = "Code";

	public static $searchable_fields = array(
		"Code",
		"Name" => "PartialMatchFilter"
	);

	public static $field_labels = array(
		"Code" => "Code",
		"Name" => "Long Name",
		"DefaultValue" => "exchange rate if no live value is available",
		"Multiplier" => "additional mupliplier to account for additional cost (e.g. 1.1)"
	);

	public static $summary_fields = array(
		"Code",
		"Name",
		"DefaultValue",
		"Multiplier"
	);

	public static $singular_name = "Alternative Currency";

	public static $plural_name = "Alternative Currencies";

}