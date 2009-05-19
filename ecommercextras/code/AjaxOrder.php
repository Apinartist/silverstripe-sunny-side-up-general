<?php

class AjaxOrder extends DataObjectDecorator {

	function Cart() {
		HTTP::set_cache_age(0);
		return ShoppingCart::current_order();
	}
/*
	function addLink() {
		return ShoppingCart_Controller::add_item_link($this->_productID);
	}

	function removeLink() {
		return ShoppingCart_Controller::remove_item_link($this->_productID);
	}

	function removeallLink() {
		return ShoppingCart_Controller::remove_all_item_link($this->_productID);
	}

	function setquantityLink() {
		return ShoppingCart_Controller::set_quantity_item_link($this->_productID);
	}
*/
	function IsCheckoutPage() {
		return ("CheckoutPage" == $this->owner->ClassName);
	}

	function CheckoutLink() {
		return CheckoutPage::find_link();
	}

	function ajaxGetCart() {
		return $this->renderWith("AjaxCart");
	}

	function ajaxRemoveItem() {
		$id = Director::urlParam("ID");
		ShoppingCart::remove_all_item($id);
		return $this->renderWith("AjaxCart");
	}

	function ajaxAddItem() {
		$id = Director::urlParam("ID");
		$item = DataObject::get_by_id("Product", $id);
		ShoppingCart::add_new_item(new Product_OrderItem($item));
		return $this->renderWith("AjaxCart");
	}


	function ajaxStartAgain() {
		ShoppingCart::clear();
		if($m = Member::currentUser()) {
			$m->logout();
		}
		for($i = 0; $i < 5; $i++) {
			$_SESSION = array();
			unset($_SESSION);
			ShoppingCart::clear();
		}
		$id = Director::urlParam("ID");
		if(!$id) {
			Director::redirect("home/ajaxStartAgain/1");
		}
		else {
			return array();
		}
	}





}

class AjaxOrder_controller extends Extension {

}