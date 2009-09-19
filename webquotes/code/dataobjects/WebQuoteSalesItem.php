<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteSalesItem extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"SortNumber" => "Int",
		"Cost" => "Decimal(9,2)",
		"Description" => "Text",
		"TimeBased" => "Boolean",
		"ExampleURL" => "Varchar(255)"
	);

	public static $casting = array();

	public static $indexes = array(
		"Name" => true
	);

	public static $defaults = array(
		"TimeBased" => 0
	);

	public static $default_records = array();

	public static $has_one = array(
		"DesignAspect" => "WebQuoteDesignAspect",
		"SalesGroup" => "WebQuoteSalesGroup"
	);

	public static $has_many = null;

	public static $many_many = null;

	public static $many_many_extraFields = null;

	public static $belongs_many_many = array(
		"Template" => "WebQuoteTemplate"
		"Quote" => "WebQuoteTemplate"
	);

	public static $default_sort = "SortNumber";

	public static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"Cost",
		"TimeBased",
		"ExampleURL"
	);

	public static $field_labels = array(
	);

	public static $summary_fields = array(
		"SortNumber",
		"Name",
		"SalesGroup.Name",
		"TimeBased",
		"Cost"
	);

	public static $singular_name = "Sales Item";

	public static $plural_name = "Sales Items";

}