<?php

/**
 * shows a list of recommended products
 * the product page / dataobject need to have a function RecommendedProductsForCart
 * which returns an array of IDs
 * @package ecommerce
 */
class RecommendedProducts extends OrderModifier {

//settings ======================================

	static $db = array();


//form NOTE THEY ARE ALL STATIC ====================================

	static function show_form() {
		return true;
	}

	static function get_form($controller) {
		return new RecommendedProducts_Form($controller, 'RecommendedProducts');
	}

// calculations ==================================

	function LiveAmount() {
		return 0;
	}

// display functions ==================================


	function ShowInCart() {
		return false;
	}

	function TableTitle() {
		"Recommended Products";
	}

	function CartTitle() {
		return $this->TableTitle();
	}

// database functions ==================================

}

class RecommendedProducts_Form extends Form {

	private static $more_details_link_text = "product details ...";

	static function set_more_details_link_text($v) {self::$more_details_link_text = $v;}

	function __construct($controller, $name) {
		Requirements::javascript("mysite/javascript/RecommendedProducts.js");
		Requirements::themedCSS("RecommendedProducts");
		$InCartIDArray = array();
		$recommendedProductsIDArray = array();
		$fieldsArray = array();
		$items = ShoppingCart::get_items();
		foreach($items as $item) {
			$InCartIDArray[] = $item->Product()->ID;
		}
		foreach($items as $item) {
			//get recommended products
			if($item) {
				$product = $item->Product();
				if($product) {
					unset($tempIDArray);
					$tempIDArray = array();
					$tempIDArray = $product->RecommendedProductsForCart();
					foreach($tempIDArray as $ID) {
						if(!in_array($ID, $InCartIDArray)) {
							$recommendedProductsIDArray[$ID] = $ID;
						}
					}
				}
			}
		}
		if(count($recommendedProductsIDArray)) {
			$fieldsArray[] = new HeaderField("Recommended Products With Your Order");
			foreach($recommendedProductsIDArray as $ID) {
				$product = DataObject::get_by_id("SiteTree", $ID);
				//foreach product in cart get recommended products
				$fieldsArray[] = new CheckboxField($product->URLSegment, $product->Title);
				$fieldsArray[] = new LiteralField($product->URLSegment."-moreinfo", '<a href="'.$product->Link().'">'.self::$more_details_link_text.'</a>');
			}
			$actions = new FieldSet(new FormAction('processOrder', 'Add selected recommended products'));
		}
		else {
			$fieldsArray[] = new HeaderField("There are no recommended products with this order.");
			$actions = new FieldSet();
		}
		$requiredFields = null;
		// 3) Put all the fields in one FieldSet
		$fields = new FieldSet($fieldsArray);

		// 6) Form construction
		return parent::__construct($controller, $name, $fields, $actions, $requiredFields);
	}



	public function processOrder($data, $form) {
		$items = ShoppingCart::get_items();
		$URLSegments = array();
		foreach($data as $key => $value) {
			if(1 == $value) {
				$URLSegments[$key] = $key;
			}
		}
		if(is_array($URLSegments) && count($URLSegments)) {
			$itemsToAdd = DataObject::get("Product", 'URLSegment IN ("'.implode('","', $URLSegments).'")');
			foreach($itemsToAdd as $item) {
				ShoppingCart::add_new_item(new Product_OrderItem($item));
			}
		}
		if(Director::is_ajax()) {
			return $this->controller->renderWith("AjaxCheckoutCart");
		}
		else {
			Director::redirect(CheckoutPage::find_link());
		}
		return;
	}

}
