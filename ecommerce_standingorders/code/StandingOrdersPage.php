<?php
/**
 * Account page shows order history and a form to allow
 * the member to edit his/her details.
 * 
 * @package ecommerce
 */
class StandingOrdersPage extends AccountPage {
	
	public static $add_action = 'a Standing Orders Page';

	/**
	 * Return a link to view the order on the account page.
	 *
	 * @param int|string $orderID ID of the order
	 * @param boolean $urlSegment Return the URLSegment only
	 */
	public static function get_standing_order_link($orderID, $urlSegment = false) {
		if(!$page = DataObject::get_one(__CLASS__)) {
			user_error('No StandingOrderPage was found. Please create one in the CMS!', E_USER_ERROR);
		}
		
		return ($urlSegment ? $page->URLSegment . '/' : $page->Link()) . 'standing-order/view/' . $orderID;
	}
	
	/**
	 * Returns all {@link Order} records for this
	 * member that are completed.
	 *
	 * @return DataObjectSet
	 */
	public function StandingOrders() {
		$memberID = Member::currentUserID();
		return DataObject::get('StandingOrder', "MemberID = '$memberID'", "Created DESC");
	}
	
	/**
	 * Automatically create an AccountPage if one is not found
	 * on the site at the time the database is built (dev/build).
	 */
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		
		if(!DataObject::get_one('StandingOrdersPage')) {
			$page = new StandingOrdersPage();
			$page->Title = 'Standing Orders';
			$page->Content = '<p>This is the standing orders account page. It is used for shop users to login and create or change their standing orders.</p>';
			$page->URLSegment = 'standing-orders';
			$page->ShowInMenus = 0;
			$page->writeToStage('Stage');
			$page->publish('Stage', 'Live');
			
			if(method_exists('DB', 'alteration_message')) DB::alteration_message('Standing Order page \'Standing Orders\' created', 'created');
		}
	}
}

class StandingOrdersPage_Controller extends AccountPage_Controller {
	
	function init() {
		parent::init();

		if(!Member::currentUserID()) {
			$messages = array(
				'default' => '<p class="message good">' . _t('AccountPage.Message', 'You\'ll need to login before you can access the standing orders page. If you are not registered, you won\'t be able to access it until you make your first order, otherwise please enter your details below.') . '</p>',
				'logInAgain' => 'You have been logged out. If you would like to log in again, please do so below.'
			);
			
			Security::permissionFailure($this, $messages);
			return false;
		}
	}
	
	/**
	 * Return the {@link Order} details for the current
	 * Order ID that we're viewing (ID parameter in URL).
	 *
	 * @return array of template variables
	 */
	public function standing_order($request) {
		Requirements::themedCSS('Order');
		Requirements::themedCSS('Order_print', 'print');
		
		$memberID = Member::currentUserID();
		$accountPageLink = AccountPage::find_link();
		$orderID = $request->param('OtherID');
		
		switch($request->param('ID')) {
			case 'create':
				$order = isset($orderID) ? DataObject::get_by_id('Order', $orderID): $this->Order();
	
				$params = array(
					'Order' => $order,
				);
				
				return $this->renderWith(array('StandingOrdersPage_edit', 'Page'), $params);
			case 'update':
				$order = $this->Order();
				
				$params = array(
					'Order' => $order,
				);
				
				return $this->renderWith(array('StandingOrdersPage_edit', 'Page'), $params);
			case 'modify':
				if(isset($orderID)) {
					$standingOrder = DataObject::get_by_id('StandingOrder', $orderID);
		
					$items = ShoppingCart::get_items();
					
					if($items) {
						foreach($items as $item) {
							ShoppingCart::remove_all_item($item->getProductID());
						}
					}
					
					//fill cart with standing order items
					$orderItems = $standingOrder->OrderItems();
		
					if($orderItems || false) {
						foreach($orderItems as $orderItem) {
								ShoppingCart::add_new_item(new Product_OrderItem(
								array(
									'ProductID' => $orderItem->ProductID,
									'ProductVersion' => $orderItem->ProductVersion(),
									'Quantity' => $orderItem->Quantity,
								),
								$orderItem->Quantity
							));
						}
					}
					
					//save session identifier for editing standing order
					Session::set('StandingOrder', $orderID);
					
					Director::redirect(CheckoutPage::find_link());
					
					$params = array();
				}
				else {
					$params = array(
						'StandingOrder' => false,
						'Message' => 'There is no order by that ID. You can <a href="' . $accountPageLink . '">edit your own personal details and view your orders.</a>.'
					);	
				}
				
				return $this->renderWith(array('StandingOrdersPage_view', 'Page'), $params);
			case 'view':
				if($orderID) {
					if($order = DataObject::get_one('StandingOrder', "StandingOrder.ID = '$orderID' AND MemberID = '$memberID'")) {
						$params = array(
							'StandingOrder' => $order
						);
					} else {
						$params = array(
							'StandingOrder' => false,
							'Message' => 'You do not have any order corresponding to this ID. However, you can <a href="' . $accountPageLink . '">edit your own personal details and view your orders.</a>.'
						);
					}
				} else {
					$params = array(
						'StandingOrder' => false,
						'Message' => 'There is no order by that ID. You can <a href="' . $accountPageLink . '">edit your own personal details and view your orders.</a>.'
					);
				}
				
				return $this->renderWith(array('StandingOrdersPage_view', 'Page'), $params);
		}
		
	}

	public function StandingOrderForm() {
		$action = $this->urlParams['ID'];
		$orderID = $this->urlParams['OtherID'];

		if($action == 'create' || isset($_REQUEST['action_doCreate'])) {
			if(isset($_REQUEST['action_doCreate'])) $orderID = $_REQUEST['OrderID'];
			return new StandingOrderForm($this, 'StandingOrderForm', $orderID);
		}
		else if($action == 'update' || isset($_REQUEST['action_doSave'])) {
			if(isset($_REQUEST['action_doSave'])) $orderID = $_REQUEST['StandingOrderID'];
			return new StandingOrderForm($this, 'StandingOrderForm', $orderID, true);
		}
	}
	
	public function Order() {
		//Create an Order to use
		$order = new Order();

		$order->MemberID = Member::currentUserID();
		
		return $order;
	}
	
}