<?php
 /**
  * Product Group is a 'holder' for Products within the CMS
  * It contains functions for versioning child products
  *
  * @package ecommerce
  */
class DropAndDragProductGroup extends ProductGroup {

	static $can_create = false;

	public function canCreate() {
		return self::$can_create;
	}

	static function set_can_create($v) {
		self::$can_create = $v;
		DropAndDragProduct::$can_create = $v;
	}

	static $db = array(
		"IsHighUsageGroup" => "Boolean",
		"Importer" => "Boolean"
	);

	public static $belongs_many_many = array(
		'DropAndDragProducts' => 'DropAndDragProduct'
	);

	public static $defaults = array();

	public static $casting = array();

	static $default_child = 'DropAndDragProduct';

	static $add_action = 'a DropAndDrag Product Group Page';

	static $icon = 'ecommercextras/images/treeicons/DropAndDragProductGroup';

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
	function DropAndDragProducts() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return DataObject::get('DropAndDragProduct', "{$bt}ShowInMenus{$bt} = 1");
	}

	/**
	 * Return children ProductGroup pages of this group.
	 * @return DataObjectSet
	 */
	function ChildGroups() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return DataObject::get("DropAndDragProductGroup", "{$bt}ParentID{$bt} = '$this->ID' AND {$bt}ShowInMenus{$bt} = 1");
	}

	/**
	 * Recursively generate a product menu.
	 * @return DataObjectSet
	 */
	function GroupsMenu() {
		if($parent = $this->Parent()) {
			return $parent instanceof DropAndDragProductGroup ? $parent->GroupsMenu() : $this->ChildGroups();
		} else {
			return $this->ChildGroups();
		}
	}

	function requireDefaultRecords() {
		//bypassing Product
		singleton('Page')->requireDefaultRecords();
		if(self::$can_create) {

			if(!DataObject::get_one('DropAndDragProductGroup')) {
				$page1 = new DropAndDragProductGroup();
				$page1->Title = 'Products';
				$page1->Content = "
					<p>This is the top level products page, it uses the <em>product group</em> page type, and it allows you to show your products checked as 'featured' on it. It also allows you to nest <em>product group</em> pages inside it.</p>
					<p>For example, you have a product group called 'DVDs', and inside you have more product groups like 'sci-fi', 'horrors' or 'action'.</p>
					<p>In this example we have setup a main product group (this page), with a nested product group containing 2 example products.</p>
				";
				$page1->URLSegment = 'products';
				$page1->writeToStage('Stage');
				$page1->publish('Stage', 'Live');
				DB::alteration_message('Product group page \'Products\' created', 'created');

				$page2 = new DropAndDragProductGroup();
				$page2->Title = 'Example product group';
				$page2->Content = '<p>This is a nested <em>product group</em> within the main <em>product group</em> page. You can add a paragraph here to describe what this product group is about, and what sort of products you can expect to find in it.</p>';
				$page2->URLSegment = 'example-product-group';
				$page2->ParentID = $page1->ID;
				$page2->writeToStage('Stage');
				$page2->publish('Stage', 'Live');
				DB::alteration_message('Product group page \'Example product group\' created', 'created');
			}
		}
	}


}
class DropAndDragProductGroup_Controller extends ProductGroup_Controller {

	function init() {
		parent::init();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript("ecommercedropanddragcart/javascript/dragToCart.js");
		Requirements::javascript("ecommercedropanddragcart/javascript/jquery-ui-1.6.custom.min.js");
		Requirements::themedCSS("dragToCart");
		Requirements::customScript("dragToCart.AjaxDropAndDragCartURL = '".$this->URLSegment."/DropAndDragcart/';");
	}

	function DropAndDragcart() {
		$id = intval(Director::URLParam("ID") - 0);
		$page = DataObject::get_by_id("DropAndDragProduct", $id);
		if($page->AllowPurchase() && $page->Variations()->Count() == 0) {
			ShoppingCart::add_new_item(new Product_OrderItem($page));
		}
		return $this->renderWith("AjaxDropAndDragCart");
	}


}
