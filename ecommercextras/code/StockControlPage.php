<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: this is an extra page which allows you to manage your shop
*/


class StockControlPage extends Page {

	static $icon = "mysite/images/treeicons/StockControlPage";

	static $defaults = array(
		"ShowInMenus" => 0,
		"ShowInSearch" => 0
	);

	function canCreate() {
		return !DataObject::get_one("SiteTree", "`ClassName` = 'StockControlPage'");
	}


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Content.StockQuantities', new TableField
			($name ="ProductStockQuantity",
			$sourceClass = "ProductStockQuantity",
			$fieldList = array(
				"MenuTitle" => "Product Name",
				"Quantity" => "BaseQuantity"
			),
			$fieldTypes = array(),
			$filterField = null,
			$sourceFilter = null,
			$editExisting = true,
			$sourceSort = null,
			$sourceJoin = "Inner Join SiteTree ON ParentID = SiteTree.ID"
		));

		return $fields;
	}

}

class StockControlPage_Controller extends Page_Controller {

	function init() {
		// Only administrators can run this method
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		parent::init();
		Requirements::themedCSS("StockControlPage");
		Requirements::javascript("ecommercextras/javascript/StockControlPage.js");
	}


}