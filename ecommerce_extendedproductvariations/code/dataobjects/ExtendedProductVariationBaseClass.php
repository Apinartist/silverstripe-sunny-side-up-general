<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 */

class ExtendedProductVariationBaseClass extends DataObject {

	public static $db = array(
		"Code" => "Varchar(12)",
		"Name" => "Varchar(255)"
	);

	public static $default_sort = "Name";

	public static $indexes = array(
	);

	public static $has_one = array(
	);

	public static $many_many = array(
	);

	public static $searchable_fields = array(
		"Code",
		"Name" => "PartialMatchFilter"
	);

	public static $field_labels = array(
		"Code" => "Short Code",
		"Name" => "Long Name",
	);

	public static $summary_fields = array(
		"Code",
		"Name"
	);

	public static $singular_name = "Product Variation - NOT CURRENTLY IN USE";
		static function set_singular_name($string) {self::$singular_name = $string; self::$variation_in_use = true;}

	public static $plural_name = "Product Variations - NOT CURRENTLY IN USE";
		static function set_plural_name($string) {self::$plural_name = $string;}

	protected static $show_in_combined_product_code = true;
		static function set_show_in_product_code($boolean) {self::$show_in_product_code = $boolean ? true : false;}

	protected static $combined_product_code_prefix = ", ";
		static function set_combined_product_code_prefix($string) {self::$combined_product_code_prefix = $string;}

	protected static $combined_product_code_postfix = "";
		static function set_combined_product_code_postfix($string) {self::$combined_product_code_postfix = $string;}

	protected static $variation_number = 0;

	protected static $variation_in_use = false;

	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}

}