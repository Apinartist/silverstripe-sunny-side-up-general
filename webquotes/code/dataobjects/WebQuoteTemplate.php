<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuoteTemplate extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"SortNumber" => "Int"
	);

	public static $many_many = array(
		"SalesItem" => "WebQuoteSalesItem"
	);

	public static $many_many_extraFields = array(
		"Quantity" => "Int"
	);

	public static $default_sort = "SortNumber";

	public static $summary_fields = array(
		"Name",
		"SortNumber"
	);

	public static $singular_name = "Quote Template";

	public static $plural_name = "Quote Templates";

}