<?php

class AjaxOrder extends DataObjectDecorator {

	function Cart() {
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
		Requirements::javascript("ecommercextras/javascript/ajaxcart.js");
		return $this->owner->URLSegment."/additemwithajax/".$this->owner->ID.'/';
	}

	function removeLinkAjax() {
		Requirements::javascript("ecommercextras/javascript/ajaxcart.js");
		return $this->owner->URLSegment."/removeitemwithajax/".$this->owner->ID.'/';
	}


}

class AjaxOrder_controller extends Extension {

	static $allowed_actions = array(
		"additemwithajax",
		"removeitemwithajax",
		"clearcompletecart",
		"modifierformsubmit",
		"getajaxcheckoutcart"
	);

	function additemwithajax() {
		$id = intval(Director::URLParam("ID"));
		$item = DataObject::get_by_id("Product", $id);
		if($item) {
			ShoppingCart::add_new_item(new Product_OrderItem($item));
		}
		$this->ajaxGetSimpleCart();
	}

	private function ajaxGetSimpleCart() {
		return $this->owner->renderWith("AjaxSimpleCart");
	}

	function removeitemwithajax() {
		$id = intval(Director::URLParam("ID"));
		if($item) {
			ShoppingCart::remove_all_item($id);
		}
		$this->ajaxGetSimpleCart();
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
		}
		die('<a href="/">click here to continue ...</a>');
	}

	function OrderFormWithoutShippingAddress() {
		return new OrderFormWithoutShippingAddress($this->owner, 'OrderForm');
	}
}