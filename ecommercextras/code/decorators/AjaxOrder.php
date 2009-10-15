<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: main class for ecommercextras that kicks things into place.
*/


class AjaxOrder extends DataObjectDecorator {

	protected static $loading_cart_text = "Loading Cart ...";

	protected static $in_cart_text = "In Cart";

	protected static $confirm_delete_text = "Are you sure you would like to remove this item from your cart?";

	private static $added_ajax_links = false;

	public static function set_loading_cart_text($v) {
		self::$loading_cart_text = $v;
	}

	public static function set_in_cart_text($v) {
		self::$in_cart_text = $v;
	}

	public static function set_confirm_delete_text($v) {
		self::$confirm_delete_text = $v;
	}

	public function can($member) {
		$this->addAjaxLinkRequirements();
	}

	public function Cart() {
		$this->addAjaxLinkRequirements();
		HTTP::set_cache_age(0);
		return ShoppingCart::current_order();
	}

	public function IsCheckoutPage() {
		return ("CheckoutPage" == $this->owner->ClassName);
	}

	public function CheckoutLink() {
		return CheckoutPage::find_link();
	}

	function addLinkAjax() {
		$this->addAjaxLinkRequirements();
		return $this->owner->URLSegment."/additemwithajax/".$this->owner->ID.'/';
	}

	function removeLinkAjax() {
		$this->addAjaxLinkRequirements();
		return $this->owner->URLSegment."/removeitemwithajax/".$this->owner->ID.'/';
	}

	function addAjaxLinkRequirements() {
		if(!self::$added_ajax_links) {
			Requirements::block("ecommerce/javascript/ecommerce.js");
			Requirements::themedCSS("EcommerceXtras");
			if($this->IsCheckoutPage()) {
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
}

class AjaxOrder_Controller extends Extension {

	protected static $product_classname = "Product";

	protected static $order_item_classname = "Product_OrderItem";

	static $allowed_actions = array(
		"additemwithajax",
		"removeitemwithajax",
		"clearcompletecart",
		"modifierformsubmit",
		"getajaxcheckoutcart",
		"getorderdetailsforadmin",
		"RecommendedProducts",
		"ModifierForm"
	);

	static function set_order_item_classname($v){
		self::$order_item_classname = $v;
	}

	static function set_product_classname($v){
		self::$product_classname = $v;
	}

	function additemwithajax() {
		$id = intval(Director::URLParam("ID"));
		if($id) {
			$item = DataObject::get_by_id(self::$product_classname, $id);
			if($item) {
				$orderItem = new self::$order_item_classname($item);
				ShoppingCart::add_new_item($orderItem);
			}
		}
		return $this->ajaxGetSimpleCart();
	}

	private function ajaxGetSimpleCart() {
		return $this->owner->renderWith("AjaxSimpleCart");
	}

	function removeitemwithajax() {
		$id = intval(Director::URLParam("ID"));
		if($item) {
			ShoppingCart::remove_all_item($id);
		}
		return $this->ajaxGetSimpleCart();
	}

	function getajaxcheckoutcart() {
		return $this->owner->renderWith("AjaxCheckoutCart");
	}

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


	function clearcompletecart() {
		ShoppingCart::clear();
		if($m = Member::currentUser()) {
			$m->logout();
		}
		for($i = 0; $i < 5; $i++) {
			$_SESSION = array();
			unset($_SESSION);
			ShoppingCart::clear();
			$_SESSION = array();
			unset($_SESSION);
		}
		die('<a href="/">Shopping cart has been removed, click here to continue ...</a>');
	}

	function OrderFormWithoutShippingAddress() {
		return new OrderFormWithoutShippingAddress($this->owner, 'OrderForm');
	}

	function getorderdetailsforadmin() {
		$m = Member::currentUser();
		if($m) {
			if($m->isAdmin()) {
				$orderID = intval(Director::URLParam("ID"));
				$dos = DataObject::get('OrderModifier', "`OrderID` = '$orderID'");
				print_r($dos);

			}
		}
	}

}

