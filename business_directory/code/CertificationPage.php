<?php
/**
 * ProductPage.php: Sub-class of Page
 * Contains info about a product
 * @created 14/10/2008
 */

class CertificationPage extends Page {

	static $icon = "business_directory/images/treeicons/CertificationPage";

	static $allowed_children = "none";

	static $has_one = array(
		"Logo" => "Image"
	);
	static $belongs_many_many = array(
		'Businesses' => 'BusinessPage'
	);

	public function canCreate() {
		return true;
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

class CertificationPage_Controller extends Page_Controller {
	function init() {
		parent::init();
	}
	/*
		This function should be overriden in page-types where not random image is required
	*/

	function SidebarImage() {
		return false;
	}

}
