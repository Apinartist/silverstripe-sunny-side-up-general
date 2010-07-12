<?php


class DropAndDragProduct extends Product {

	static $can_create = false;

	public function canCreate() {
		return self::$can_create;
	}

	static function set_can_create($v) {
		self::$can_create = $v;
		DropAndDragProductGroup::$can_create = $v;
	}

	static $db = array (

	);

	static $hide_ancestor = true;

	static $icon = "ecommercextras/images/treeicons/DropAndDragProduct";

	static $add_action = "a DropAndDrag product";

	public static $many_many = array(
		'DropAndDragProductGroups' => 'DropAndDragProductGroup'
	);

	public static $belongs_many_many = array();

	function getCMSFields() {
		$fields = parent::getCMSFields();
		// standard extra fields like weight and price
		return $fields;
	}

	function getVariationsTable() {
		return new HeaderField("Not available", "Variations are not available");
	}

	/* IMPORTANT ! */
	public function Product($current = false) {
		if($current) return DataObject::get_by_id('DropAndDragProduct', $this->_productID);
		else return Versioned::get_version('DropAndDragProductProduct', $this->_productID, $this->_productVersion);
	}

	public function TableTitle() {
		return "-----------";
	}

	function requireDefaultRecords() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		parent::requireDefaultRecords();
		if(self::$can_create) {
			Db::query("Update SiteTree_versions Set {$bt}ClassName{$bt} = 'DropAndDragProductGroup' where {$bt}ClassName{$bt} = 'ProductGroup';");
			Db::query("Update SiteTree_Live Set {$bt}ClassName{$bt} = 'DropAndDragProductGroup' where {$bt}ClassName{$bt} = 'ProductGroup';");
			Db::query("Update SiteTree Set {$bt}ClassName{$bt} = 'DropAndDragProductGroup' where {$bt}ClassName{$bt} = 'ProductGroup';");

			Db::query("Update SiteTree_Live Set {$bt}ClassName{$bt} = 'DropAndDragProduct' where {$bt}ClassName{$bt} = 'Product';");
			Db::query("Update SiteTree_versions Set {$bt}ClassName{$bt} = 'DropAndDragProduct' where {$bt}ClassName{$bt} = 'Product';");
			Db::query("Update SiteTree Set {$bt}ClassName{$bt} = 'DropAndDragProduct' where {$bt}ClassName{$bt} = 'Product';");

		}
	}


}

class DropAndDragProduct_Controller extends Product_Controller {


}

