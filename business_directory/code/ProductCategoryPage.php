<?php
/**
 * ProductPage.php: Sub-class of Page
 * Contains info about a product
 * @created 14/10/2008
 */

class ProductCategoryPage extends Page {
	static $icon = "business_directory/images/treeicons/ProductCategoryPage";

	static $allowed_children = "none";

	static $default_child = "";
	static $default_parent = "";
	static $can_be_root = false;
	//static $need_permission = null;
	//static $hide_ancestor = null;

	static $db = array();
	static $has_one = array();
	//static $has_many = array(	);
	//static $many_many = array();
	static $belongs_many_many = array(
		'Businesses' => 'BusinessPage'
	);
	//static $many_many_extraFields = array();
	//static $casting = array();

	//static $indexes = array();
	//static $defaults = array();

	//static $versioning = array();
	//static $default_sort = "Sort";
	//static $add_action = null;
	//static $can_create = true;
	//static $extensions = array();
	//public static $breadcrumbs_delimiter = " &raquo; ";

	public function canCreate() {
		return true;
	}

	public function canDelete() {
		false;
	}

	static $defaults = array (
		"HasGeoInfo" => 0
	);

	function getCMSFields( $cms ) {
		$fields = parent::getCMSFields( $cms );
		$fields->removeFieldFromTab("Root.Content", "Map" );
		return $fields;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
	}
	function setSidebarImage() {
		return false;
	}
}

class ProductCategoryPage_Controller extends Page_Controller {
	function init() {
		parent::init();
	}

	/*
		This function should be overriden in page-types where not random image is required
	*/

	function SidebarImage() {
		return false;
	}


	function RelatedProducts() {
	}

}
