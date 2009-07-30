<?php

/**
 * shows a list of recommended products
 * the product page / dataobject need to have a function RecommendedProductsForCart
 * which returns an array of IDs
 * @package ecommerce
 */
class RecommendedProductsModifier extends OrderModifier {

// 					 *** static variables
	protected static $image_width = 100;

	static $db = array();

// 					 *** static functions
	static function show_form() {
		return true;
	}

	static function get_form($controller) {
		return new RecommendedProductsModifier_Form($controller, 'RecommendedProducts');
	}

// 					 *** display functions
	function ShowInTable() {
		return false;
	}

	function CanRemove() {
		return false;
	}

// 					 *** custom values
	static function set_image_width($v) {
		self::$image_width;
	}

	static function get_image_width() {
		return self::$image_width;
	}

// 					 *** table values
	function LiveAmount() {
		return 0;
	}
	function TableValue() {
		return "";
	}

// 					 *** table titles
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

// 					 ***  database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}
}

class RecommendedProductsModifier_Form extends Form {

	private static $more_details_link_text = "product details ...";

	private static $nothing_recommended_text = " ";

	private static $something_recommended_text = "Recommended Additions";

	private static $add_button_text = "Add Selected Items";

	static function set_more_details_link_text($v) {self::$more_details_link_text = $v;}

	static function set_nothing_recommended_text($v) {self::$nothing_recommended_text = $v;}

	static function set_something_recommended_text($v) {self::$something_recommended_text = $v;}

	static function set_add_button_text($v) {self::$add_button_text = $v;}

	function __construct($controller, $name) {
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
			Requirements::javascript("jsparty/jquery/jquery.js");
			Requirements::javascript("jsparty/jquery/plugins/livequery/jquery.livequery.js");
			Requirements::javascript("ecommercextras/javascript/RecommendedProductsModifier.js");
			Requirements::themedCSS("RecommendedProducts");
			$fieldsArray[] = new HeaderField(self::$something_recommended_text);
			foreach($recommendedProductsIDArray as $ID) {
				$product = DataObject::get_by_id("SiteTree", $ID);
				//foreach product in cart get recommended products
				$fieldsArray[] = new CheckboxField($product->URLSegment, $product->Title);
				$imageID = $product->ImageID;
				$secondPart = '';
				if($product->ImageID > 0) {
					$resizedImage = $product->Image()->SetWidth(RecommendedProductsModifier::get_image_width());
					$imageLink = $resizedImage->Filename;
					$secondPart = '<span class="secondPart"><img src="'.$imageLink.'" alt="'.$product->Title.'" /></span>';
				}
				$firstPart = '<span class="firstPart">'.self::$more_details_link_text.'</span>';
				$fieldsArray[] = new LiteralField($product->URLSegment."-moreinfo", '<div class="RecommendProductSection"><a href="'.$product->Link().'">'.$firstPart.$secondPart.'</a></div>');
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
