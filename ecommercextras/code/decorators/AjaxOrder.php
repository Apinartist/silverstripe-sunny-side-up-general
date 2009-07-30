<?php

class AjaxOrder extends DataObjectDecorator {

	function Cart() {
		$this->addAjaxLinkRequirements();
		HTTP::set_cache_age(0);
		return ShoppingCart::current_order();
	}
	function IsCheckoutPage() {
		return ("CheckoutPage" == $this->owner->ClassName);
	}

	function CheckoutLink() {
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
		Requirements::block("ecommerce/javascript/ecommerce.js");
		Requirements::javascript("jsparty/jquery/plugins/livequery/jquery.livequery.js");
		Requirements::javascript("ecommercextras/javascript/AjaxOrder.js");
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
		"RecommendedProducts"
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
			if($order && $order->MemberID == Member::currentUserID()) {
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
		die('<a href="/">click here to continue ...</a>');
	}

	function OrderFormWithoutShippingAddress() {
		return new OrderFormWithoutShippingAddress($this->owner, 'OrderForm');
	}
}

