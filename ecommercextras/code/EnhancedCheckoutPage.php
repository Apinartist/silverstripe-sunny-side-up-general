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

	/*
	function canCreate() {
		return !DataObject::get_one("Page", "`ClassName`= 'CheckoutPage' OR `ClassName`= 'EnhancedCheckoutPage'");
	}
	*/

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