<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: this is an extra page which allows you to manage your shop
*/


class ShopManagerPage extends Page {

	static $icon = "mysite/images/treeicons/ShopManagerPage";

	static $defaults = array(
		"ShowInMenus" => 0,
		"ShowInSearch" => 0
	);

	function canCreate() {
		return !DataObject::get_one("SiteTree", 'ClassName = "ShopManagerPage"');
	}

}

class ShopManagerPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		// Only administrators can run this method
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this,
				_t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it.
				Enter your credentials below and we will send you right along.'));
			return;
		}
		Requirements::themedCSS("ShopManagerPage");
		Requirements::javascript("ecommercextras/javascript/ShopManagerPage.js");
	}

	function CheckoutPage() {
		return DataObject::get_one("CheckoutPage");
	}

	function AccountPage() {
		return DataObject::get_one("AccountPage");
	}

	function LastOrders() {
		return DataObject::get("Order", "", "Created DESC", "", "0, 250");
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


	function getorderdetailsforadmin() {
		$orderID = intval(Director::URLParam("ID"));
		$dos = DataObject::get('OrderModifier', "`OrderID` = '$orderID'");
		$v = print_r($dos);
		$this->Content = $v;
		return array();
	}

	function testorderreceipt() {
		$orderID = intval(Director::URLParam("ID"));
		if(!$orderID) {
			$o = DataObject::get_one("Order", "", "Created DESC");
			if($o) {
				$orderID = $o->ID;
			}
		}
		if($orderID) {
			$order = DataObject::get_by_id("Order", $orderID);
			if($order) {
				$from = Email::getAdminEmail() . " NOTE - THIS IS A GUESS VALUE ONLY";
				$to = $order->Member()->Email;
				$subject = "Shop Sale Information #$order->ID - TEST SUBJECT ONLY!";

				$purchaseCompleteMessage = DataObject::get_one('CheckoutPage')->PurchaseComplete;
				$emailClass = 'Order_ReceiptEmail';
				if($c = Director::URLParam("OtherID")) {
					if(class_exists($c)) {
						$c = $emailClass = $c;
					}
				}
				$email = new $emailClass();
				$email->setFrom($from);
				$email->setTo($to);
				$email->setSubject($subject);
				$email->populateTemplate(
					array(
						'PurchaseCompleteMessage' => $purchaseCompleteMessage,
						'Order' => $order
					)
				);
				Requirements::clear();

				$v = $email->debug();
				$this->Content = $v;
			}
		}
		else {
			$this->Content = "<h1>NO ORDER FOUND!</h1>";
		}
		return array();
	}




}