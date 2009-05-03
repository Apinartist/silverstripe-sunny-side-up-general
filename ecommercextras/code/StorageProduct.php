<?php


class StorageProduct extends Product {

	static $can_create = false;

	public function canCreate() {
		return self::$can_create;
	}

	static function set_can_create($v) {
		self::$can_create = $v;
		StorageProductGroup::$can_create = $v;
	}

	static $db = array (
		"COD" => "Int(11)",
		"CUS" => "Varchar(255)",
		"DSH" => "Varchar(255)",
		"DSL" => "Text",
		"IOS" => "Boolean",
		"KEW" => "Varchar(255)",
		"SPC" => "Text",
		"MGP" => "Double(7,1)",
		"NAM" => "Varchar(255)",
		"OID" => "Int(11)"/*
		PER - per field (e.g. per 100)
		SPE - special
		ZOR - zoom range
		STA - status
		MIN - minimum order
		*/
	);

	static $hide_ancestor = true;

	static $icon = "ecommercextras/images/treeicons/StorageProduct";

	static $add_action = "a Storage product";

	public static $many_many = array(
		'StorageProductGroups' => 'StorageProductGroup'
	);

	public static $belongs_many_many = array();

	function getCMSFields() {
		$fields = parent::getCMSFields();
		// standard extra fields like weight and price
		$fields->removeFieldFromTab("Root.Content.Main", "Weight");
		$fields->removeFieldFromTab("Root.Content.Main", "Model");
		$fields->removeFieldFromTab("Root.Content.Main", "Price");
		$fields->removeFieldFromTab("Root.Content.Variations", "ProductVariation");
		return $fields;
	}

	function getVariationsTable() {
		return new HeaderField("Not available", "Variations are not available");
	}

}

class StorageProduct_Controller extends Product_Controller {


}

