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

	public static $has_many = array(
		"SalesItem" => "WebQuoteSalesItem"
	);

	public static $default_sort = "SortNumber";

	public static $summary_fields = array(
		"Name",
		"SortNumber"
	);

	public static $singular_name = "Sales Group";

	public static $plural_name = "Sales Groups";

}