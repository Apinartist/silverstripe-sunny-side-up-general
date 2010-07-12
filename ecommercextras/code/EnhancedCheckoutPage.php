<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description: fixes some bugs in the checkout page
 *
 **/

class EnhancedCheckoutPage extends CheckoutPage {

	static $icon = "ecommercextras/images/treeicons/EnhancedCheckoutPage";

	static $hide_ancestor = "CheckoutPage";

	static $add_action = 'The Checkout Page';

	protected static $autocreate_enhancedcheckoutpage = false;
		static function set_autocreate_enhancedcheckoutpage($v) {self::$autocreate_enhancedcheckoutpage = $v;}
		static function get_autocreate_enhancedcheckoutpage() {return self::$autocreate_enhancedcheckoutpage;}

	function canCreate() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return !DataObject::get_one("Page", "{$bt}ClassName{$bt} = 'EnhancedCheckoutPage'");
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(self::get_autocreate_enhancedcheckoutpage()) {
			$update = '';
			$page = DataObject::get_one("EnhancedCheckoutPage");
			if(!$page) {
				$page = new EnhancedCheckoutPage();
				$page->Title = "checkout-page";
				$page->MenuTitle = "checkout";
				$page->URLSegment = "checkout";
				$update .= "created page itself, ";
			}
			if($page) {
				if($update) {
					$page->writeToStage('Stage');
					$page->publish('Stage', 'Live');
					DB::alteration_message($page->ClassName." created/updated: ".$update.' DONE', 'created');
				}
			}
		}
	}

}

class EnhancedCheckoutPage_Controller extends CheckoutPage_Controller {


	/**
	 * Returns a message explaining why the customer
	 * can't checkout the requested order.
	 * @FIXES: select statement which only had an ID and Not Order.ID
	 * @return string
	 */
	function Message() {
		$orderID = Director::urlParam('Action');
		$checkoutLink = self::find_link();

		if($memberID = Member::currentUserID()) {
			if($order = DataObject::get_one('Order', "Order.ID = '$orderID' AND MemberID = '$memberID'")) {
				return 'You can not checkout this order because it has been already successfully completed. Click <a href="' . $order->Link() . '">here</a> to see it\'s details, otherwise you can <a href="' . $checkoutLink . '">checkout</a> your current order.';
			} else {
				return 'You do not have any order corresponding to that ID, so you can\'t checkout this order.';
			}
		} else {
			$redirectLink = CheckoutPage::get_checkout_order_link($orderID);
			return 'You can not checkout this order because you are not logged in. To do so, please <a href="Security/login?BackURL=' . $redirectLink . '">login</a> first, otherwise you can <a href="' . $checkoutLink . '">checkout</a> your current order.';
		}
	}


}
