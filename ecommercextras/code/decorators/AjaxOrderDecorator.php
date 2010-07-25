<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @description:
* it contains a whole bunch of useful functions (e.g. number of items in cart) and ajax functionality
* that is added to any page within a site
* ajax works as follows:
* - there are three types of links: add to cart, remove from cart (within cart), remove from cart (link)
* - when you click the link: add to cart or remove from cart two things change:
* * the link itself
* * a designated cart area gets a new cart
*/


class AjaxOrderDecorator extends DataObjectDecorator {

	/* why do we need this???
	public function can($member) {
		AjaxOrderDecorator::included_basic_ajax_order_link_requirements();
	}
	*/

	function addLinkAjax() {
		AjaxOrderDecorator_Controller::included_basic_ajax_order_link_requirements();
		return $this->owner->URLSegment."/additemwithajax/".$this->owner->ID.'/'.$this->owner->ClassName;
	}

	function removeLinkAjax() {
		AjaxOrderDecorator_Controller::included_basic_ajax_order_link_requirements();
		return $this->owner->URLSegment."/removeitemwithajax/".$this->owner->ID.'/';
	}

	function retrieveSimpleCartLink() {
		return $this->owner->URLSegment."/showsimplecart/";
	}

}

class AjaxOrderDecorator_Controller extends Extension {

	protected static $loading_cart_text = "Loading Cart ...";
		public static function set_loading_cart_text($v) {self::$loading_cart_text = $v;}

	protected static $in_cart_text = "In Cart";
		public static function set_in_cart_text($v) {self::$in_cart_text = $v;}

	protected static $confirm_delete_text = "Are you sure you would like to remove this item from your cart?";
		public static function set_confirm_delete_text($v) {self::$confirm_delete_text = $v;}

	private static $added_ajax_links = false;

	protected static $product_classname = "Product";
		static function set_product_classname($v){self::$product_classname = $v;}

	protected static $order_item_classname = "Product_OrderItem";
		static function set_order_item_classname($v){self::$order_item_classname = $v;}

	static $allowed_actions = array(
		"showsimplecart",
		"additemwithajax",
		"removeitemwithajax",
		"modifierformsubmit",
		"getajaxcheckoutcart",
		"RecommendedProducts",
		"ModifierForm",
		"OrderFormWithoutShippingAddress",
		"OrderFormWithShippingAddress"
	);


	public static function include_basic_ajax_order_requirements() {
		Requirements::block("ecommerce/javascript/ecommerce.js");
		Requirements::themedCSS("EcommerceXtras");
	}

	function included_basic_ajax_order_link_requirements() {
		if(!self::$added_ajax_links) {
			self::include_basic_ajax_order_requirements();
			if($this->owner instanceOf CheckoutPage) {
				Requirements::javascript("ecommercextras/javascript/AjaxCheckout.js");
			}
			else {
				Requirements::javascript("ecommercextras/javascript/AjaxCart.js");
				if(self::$loading_cart_text) {
					Requirements::customScript(
						'jQuery(document).ready(function() {AjaxCart.set_LoadingText("'.Convert::raw2js(self::$loading_cart_text).'")});',
						"AjaxOrder_set_LoadingText"
					);
				}
				if(self::$in_cart_text) {
					Requirements::customScript(
						'jQuery(document).ready(function() {AjaxCart.set_InCartText("'.Convert::raw2js(self::$in_cart_text).'")});',
						"AjaxOrder_set_InCartText"
					);
				}
				if(self::$confirm_delete_text) {
					Requirements::customScript(
						'jQuery(document).ready(function() {AjaxCart.set_ConfirmDeleteText("'.Convert::raw2js(self::$confirm_delete_text).'")});',
						"AjaxOrder_set_ConfirmDeleteText"
					);
				}
			}
			self::$added_ajax_links = true;
		}
	}

	function additemwithajax() {
		$id = intval(Director::URLParam("ID"));
		$productClassName = Director::URLParam("OtherID");
		if($id && class_exists($productClassName)) {
			$item = DataObject::get_by_id($productClassName, $id);
			if($item) {
				$orderItem = new self::$order_item_classname($item);
				ShoppingCart::add_new_item($orderItem);
			}
		}
		return $this->ajaxGetSimpleCart();
	}

	function showsimplecart() {
		return $this->ajaxGetSimpleCart();
	}


	function removeitemwithajax() {
		$id = intval(Director::URLParam("ID"));
		if($id) {
			ShoppingCart::remove_all_item($id);
		}
		return  $this->owner->renderWith("AjaxSimpleCart");
	}

	function getajaxcheckoutcart() {
		return $this->owner->renderWith("AjaxCheckoutCart");
	}

	//TEMPLATE CONTROLS ....

	function AjaxOrder() {
		if($orderID = intval(Director::urlParam('Action') + 0)) {
			$order = DataObject::get_by_id('Order', $orderID);
			if($order && ($order->MemberID == Member::currentUserID())) {
				return $order;
			}
		} else {
			return ShoppingCart::current_order();
		}
	}


	public function Cart() {
		self::included_basic_ajax_order_link_requirements();
		HTTP::set_cache_age(0);
		return ShoppingCart::current_order();
	}

	public function NumItemsInCart() {
		if($cart = $this->Cart()) {
			if($items = $cart->Items()) {
				return $items->count();
			}
		}
		return 0;
	}

	public function NumItemsTimesQuantityInCart() {
		$total = 0;
		if($cart = $this->Cart()) {
			if($items = $cart->Items()) {
				foreach($items as $item) {
					$total += $item->getQuantity();
				}
			}
		}
		return $total;
	}


	public function MoreThanOneNumItemsTimesQuantityInCart() {
		return $this->NumItemsTimesQuantityInCart() > 1;
	}

	public function MoreThanOneItemInCart() {
		return $this->NumItemsInCart() > 1;
	}

	public function SubTotalCartValue() {
		$order = ShoppingCart::current_order();
		return $order->SubTotal;
	}

	public function AccountPage() {
		return AccountPage::find_link();
	}

	public function CheckoutLink() {
		return CheckoutPage::find_link();
	}


	// ___ HELP FUNCTIONS

	private function ajaxGetSimpleCart() {
		if($this->owner->isAjax()) {
			return $this->owner->renderWith("AjaxSimpleCart");
		}
		else {
			Director::redirectBack();
		}
	}

}

