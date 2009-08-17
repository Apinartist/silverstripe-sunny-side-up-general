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

	/* IMPORTANT ! */
	public function Product($current = false) {
		if($current) return DataObject::get_by_id('StorageProduct', $this->_productID);
		else return Versioned::get_version('StorageProductProduct', $this->_productID, $this->_productVersion);
	}

	public function TableTitle() {
		return "-----------";
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(self::$can_create) {

			Db::query('Update SiteTree_versions Set `ClassName` = "StorageProductGroup" where `ClassName` = "ProductGroup";');
			Db::query('Update SiteTree_Live Set `ClassName` = "StorageProductGroup" where `ClassName` = "ProductGroup";');
			Db::query('Update SiteTree Set `ClassName` = "StorageProductGroup" where `ClassName` = "ProductGroup";');

			Db::query('Update SiteTree_Live Set `ClassName` = "StorageProduct" where `ClassName` = "Product";');
			Db::query('Update SiteTree_versions Set `ClassName` = "StorageProduct" where `ClassName` = "Product";');
			Db::query('Update SiteTree Set `ClassName` = "StorageProduct" where `ClassName` = "Product";');

			/*
			$obj = DataObject::get("SiteTree", '`ClassName` = "ProductGroup" OR `ClassName` = "Product"' );
			foreach($obj as $page) {

				$message = '';
				if($page->Parent()) {
					$message .= "checking ".$page->Title;
					$message .= " --- parent is StorageProductGroup";
					$children = $page->Children();
					if($children->count()) {
						$message .=  " --- has children";
						if($page->ClassName != "StorageProductGroup") {
							$message .=  ' --- <span style="color: red;">changing to StorageProductGroup</span>';
							$page->setClassName( "StorageProductGroup" );
							$page = $page->newClassInstance( "StorageProductGroup" );
						}
						else {
							$message .=  ' --- <span style="color: green;">className = '.$page->ClassName.'</span>';
						}
					}
					else {
						$message .=  " --- does not have children ";
						if($page->ClassName != "StorageProduct") {
							$message .=  ' --- <span style="color: red;">changing to StorageProduct</span>';
							$page->setClassName( "StorageProduct" );
							$page = $page->newClassInstance( "StorageProduct" );
							$page->AllowPurchase = 1;
						}
						else {
							$message .=  '--- <span style="color: green;">className = '.$page->ClassName.'</span>';
						}
					}
				}
				else {
					$message .=  ' --- <span style="color: red;">changing to StorageProductGroup</span>';
					$page->setClassName( "StorageProductGroup" );
					$page = $page->newClassInstance( "StorageProductGroup" );
				}
				if("StorageProduct" == $page->ClassName || "StorageProductGroup" == $page->ClassName) {
					$page->write();
					$page->writeToStage('Stage');
					$page->publish('Stage', 'Live');
				}
				$message .=  ' ||| end <hr />';
				Database::alteration_message($message);
			}
			*/
		}
	}


}

class StorageProduct_Controller extends Product_Controller {


}

