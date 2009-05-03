<?php
 /**
  * Product Group is a 'holder' for Products within the CMS
  * It contains functions for versioning child products
  *
  * @package ecommerce
  */
class StorageProductGroup extends ProductGroup {

	static $can_create = false;

	public function canCreate() {
		return self::$can_create;
	}

	static function set_can_create($v) {
		self::$can_create = $v;
		StorageProduct::$can_create = $v;
	}

	static $db = array(
		"IsHighUsageGroup" => "Boolean",
		"Importer" => "Boolean"
	);

	public static $belongs_many_many = array(
		'StorageProducts' => 'StorageProduct'
	);

	public static $defaults = array();

	public static $casting = array();

	static $default_child = 'StorageProduct';

	static $add_action = 'a Storage Product Group Page';

	static $icon = 'ecommercextras/images/treeicons/StorageProductGroup';

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

		$childProducts = DataObject::get('StorageProduct', "`ParentID` = $this->ID AND $filter");
		$relatedProducts = $this->getManyManyComponents('StorageProducts', $filter);

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
		return DataObject::get('StorageProductGroup', "`ParentID` = '$this->ID' AND `ShowInMenus` = 1");
	}

	/**
	 * Recursively generate a product menu.
	 * @return DataObjectSet
	 */
	function GroupsMenu() {
		if($parent = $this->Parent()) {
			return $parent instanceof StorageProductGroup ? $parent->GroupsMenu() : $this->ChildGroups();
		} else {
			return $this->ChildGroups();
		}
	}



}
class StorageProductGroup_Controller extends ProductGroup_Controller {

	function init() {
		parent::init();
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript("mysite/javascript/dragToCart.js");
		Requirements::javascript("mysite/javascript/jquery-ui-1.6.custom.min.js");
	}

}
?>