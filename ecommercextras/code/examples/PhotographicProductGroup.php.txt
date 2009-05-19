<?php
 /**
  * Product Group is a 'holder' for Products within the CMS
  * It contains functions for versioning child products
  *
  * @package ecommerce
  */
class PhotographicProductGroup extends ProductGroup {

	static $can_create = false;

	public function canCreate() {
		return self::$can_create;
	}

	static function set_can_create($v) {
		self::$can_create = $v;
		PhotographicProduct::$can_create = $v;
	}

	static $db = array(
		"IsHighUsageGroup" => "Boolean",
		"Importer" => "Boolean"
	);

	public static $belongs_many_many = array(
		'PhotographicProducts' => 'PhotographicProduct'
	);

	public static $defaults = array();

	public static $casting = array();

	static $default_child = 'PhotographicProduct';

	static $add_action = 'a Photographic Product Group Page';

	static $icon = 'ecommercextras/images/treeicons/PhotographicProductGroup';

	static $featured_products_permissions = array(
		'Show Only Featured Products',
		'Show All Products'
	);

	static $non_featured_products_permissions = array(
		'Show All Products'
	);


	/**
	 * Recursively create a set of {@link Product} pages
	 * that belong to this ProductGroup as a child, related
	 * Product, or through one of this ProductGroup's nested
	 * ProductGroup pages.
	 *
	 * @param string $extraFilter Additional SQL filters to apply to the Product retrieval
	 * @param array $permissions
	 * @return DataObjectSet
	 */
	function ProductsShowable() {
		$filter = "`ShowInMenus` = 1";
		$products = new DataObjectSet();

		$childProducts = DataObject::get('PhotographicProduct', "`ParentID` = $this->ID AND $filter");
		$relatedProducts = $this->getManyManyComponents('PhotographicProducts', $filter);

		if($childProducts) {
			$products->merge($childProducts);
		}

		if($relatedProducts) {
			$products->merge($relatedProducts);
		}

		if($childGroups = $this->ChildGroups()) {
			foreach($childGroups as $childGroup) {
				$products->merge($childGroup->ProductsShowable());
			}
		}

		$products->removeDuplicates();

		return $products;
	}

	/**
	 * Return children ProductGroup pages of this group.
	 * @return DataObjectSet
	 */
	function ChildGroups() {
		return DataObject::get('PhotographicProductGroup', "`ParentID` = '$this->ID' AND `ShowInMenus` = 1");
	}

	/**
	 * Recursively generate a product menu.
	 * @return DataObjectSet
	 */
	function GroupsMenu() {
		if($parent = $this->Parent()) {
			return $parent instanceof PhotographicProductGroup ? $parent->GroupsMenu() : $this->ChildGroups();
		} else {
			return $this->ChildGroups();
		}
	}



}
class PhotographicProductGroup_Controller extends ProductGroup_Controller {

	function init() {
		parent::init();
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript("mysite/javascript/dragToCart.js");
		Requirements::javascript("mysite/javascript/jquery-ui-1.6.custom.min.js");
	}

}
?>