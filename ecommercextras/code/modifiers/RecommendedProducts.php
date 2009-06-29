<?php

/**
 * shows a list of recommended products
 * the product page / dataobject need to have a function RecommendedProductsForCart
 * which returns an array of IDs
 * @package ecommerce
 */
class RecommendedProducts extends OrderModifier {

//static variables \\\

	static $db = array();


// static functions \\\
	static function show_form() {
		return true;
	}

	static function get_form($controller) {
		return new RecommendedProducts_Form($controller, 'RecommendedProducts');
	}

// display functions \\\
	function ShowInTable() {
		return false;
	}

	function CanRemove() {
		return false;
	}


// table values \\\
	function LiveAmount() {
		return 0;
	}
	function TableValue() {
		return "";
	}

//table titles \\\
	function LiveName() {
		return "Recommended Products";
	}

	function Name() {
		if($this->ID) {
			return $this->Name;
		}
		else {
			return $this->LiveName();
		}
	}

	function TableTitle() {
		return $this->Name();
	}

// database functions \\\
	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}
}

class RecommendedProducts_Form extends Form {

	private static $more_details_link_text = "product details ...";

	private static $nothing_recommended_text = " ";

	private static $something_recommended_text = "Recommended Additions";

	private static $add_button_text = "Add Selected Items";

	static function set_more_details_link_text($v) {self::$more_details_link_text = $v;}

	static function set_nothing_recommended_text($v) {self::$nothing_recommended_text = $v;}

	static function set_something_recommended_text($v) {self::$something_recommended_text = $v;}

	static function set_add_button_text($v) {self::$add_button_text = $v;}

	function __construct($controller, $name) {
		Requirements::javascript("jsparty/jquery/plugins/livequery/jquery.livequery.js");
		Requirements::javascript("ecommercextras/javascript/RecommendedProducts.js");
		Requirements::themedCSS("RecommendedProducts");
		$InCartIDArray = array();
		$recommendedProductsIDArray = array();
		$fieldsArray = array();
		$items = ShoppingCart::get_items();
		foreach($items as $item) {
			$id = $item->Product()->ID;
			$InCartIDArray[$id] = $id;
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
			$fieldsArray[] = new HeaderField(self::$something_recommended_text);
			foreach($recommendedProductsIDArray as $ID) {
				$product = DataObject::get_by_id("SiteTree", $ID);
				//foreach product in cart get recommended products
				$fieldsArray[] = new CheckboxField($product->URLSegment, $product->Title);
				$fieldsArray[] = new LiteralField($product->URLSegment."-moreinfo", '<a href="'.$product->Link().'">'.self::$more_details_link_text.'</a>');
			}
			$actions = new FieldSet(new FormAction('processOrder', self::$add_button_text));
		}
		else {
			$fieldsArray[] = new HeaderField(self::$nothing_recommended_text);
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
