<?php
/**
 * StandingOrdersPage page shows order history and a form to allow
 * the member to edit his/her details.
 *
 * @package ecommerce
 * @subpackage ecommerce ecommerce_standingorders
 * @author nicolaas [at] sunnysideup.co.nz
 */
class StandingOrdersPage extends AccountPage {

	static $db = array(
		"OrderDays" => "Varchar(255)",
		"WhatAreStandingOrders" => "HTMLText",
		"OnceLoggedInYouCanCreateStandingOrder" => "HTMLText"
	);

	protected static $week_days = array(
		"Monday" => "Monday",
		"Tuesday" => "Tuesday",
		"Wednesday" => "Wednesday",
		"Thursday" => "Thursday",
		"Friday" => "Friday",
		"Saturday" => "Saturday",
		"Sunday" => "Sunday"
	);

	public static $add_action = 'a Standing Orders Page';

	/**
	 * Return a link to view the order on the account page.
	 *
	 * @param int|string $orderID ID of the order
	 * @param boolean $urlSegment Return the URLSegment only
	 */
	public static function get_standing_order_link($action = 'view', $orderID = null, $urlSegment = false) {
		if(!$page = DataObject::get_one(__CLASS__)) {
			user_error('No StandingOrderPage was found. Please create one in the CMS!', E_USER_ERROR);
		}

		return ($urlSegment ? $page->URLSegment . '/' : $page->Link()) . 'standing-order/'.$action.'/' . $orderID;
	}

	public function canCreate($member = null) {
		return !DataObject::get_one("StandingOrdersPage");
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Settings", new TextField($name = "OrderDays", $title = "Order Weekdays - separated by comma, e.g. Monday, Tuesday, Wednesday"));
		$fields->addFieldToTab("Root.Content.ExplainingStandingOrders", new HTMLEditorField($name = "WhatAreStandingOrders", $title = "What Are Standing Orders - Explanation Used throughout the site.", $rows = 3, $cols = 3));
		$fields->addFieldToTab("Root.Content.ExplainingStandingOrders", new HTMLEditorField($name = "OnceLoggedInYouCanCreateStandingOrder", $title = "Explanation for people who are not logged-in yet explaining that they can turn an order into a standing order...", $rows = 3, $cols = 3));
		return $fields;
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

	function onBeforeWrite() {
		$days = explode(",", $this->OrderDays);
		$cleanDays = array();
		if(count($days)) {
			foreach($days as $day) {
				$day = trim($day);
				if(count(self::$week_days)) {
					foreach(self::$week_days as $perfectDay) {
						if(strtolower($day)== strtolower($perfectDay)) {
							$cleanDays[$perfectDay] = $perfectDay;
						}
					}
				}
			}
		}
		if(count($cleanDays)) {
			$this->OrderDays = implode(",", $cleanDays);
		}
		else {
			$this->OrderDays = implode(",", self::$week_days);
		}
		parent::onBeforeWrite();
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

			//Security::permissionFailure($this, $messages);
			return false;
		}
	}

	public function order($request) {
		Requirements::themedCSS('Order');
		Requirements::themedCSS('Order_print', 'print');

		$memberID = Member::currentUserID();
		$accountPageLink = AccountPage::find_link();

		if($orderID = $request->param('ID')) {
			if($order = DataObject::get_one('Order', "Order.ID = '$orderID' AND MemberID = '$memberID'")) {
				return array(
					'Order' => $order,
					'CreateLink' => StandingOrdersPage::get_standing_order_link('create', $orderID)
				);
			} else {
				return array(
					'Order' => false,
					'Message' => 'You do not have any order corresponding to this ID. However, you can <a href="' . $accountPageLink . '">edit your personal details and view your orders.</a>.'
				);
			}
		} else {
			return array(
				'Order' => false,
				'Message' => 'There is no order by that ID. You can <a href="' . $accountPageLink . '">edit your personal details and view your orders</a>.'
			);
		}
	}

	/**
	 * Return the {@link Order} details for the current
	 * Order ID that we're viewing (ID parameter in URL).
	 *
	 * @return array of template variables
	 */
	public function standing_order($request) {
		Versioned::reading_stage('Stage');

		Requirements::themedCSS('Order');
		Requirements::themedCSS('Order_print', 'print');

		$memberID = Member::currentUserID();
		$accountPageLink = AccountPage::find_link();
		$orderID = $request->param('OtherID');

		switch($request->param('ID')) {
			case 'cancel':
				if($orderID) {
					$standingOrder = DataObject::get_by_id('StandingOrder', $orderID);
					$standingOrder->Status = 'MemberCancelled';
					$standingOrder->write();

					Director::redirectBack();
				}
				break;
			case 'create':
				$order = isset($orderID) ? DataObject::get_by_id('Order', $orderID): $this->BlankOrder();

				$params = array(
					'Order' => $order,
				);

				return $this->renderWith(array('StandingOrdersPage_edit', 'Page'), $params);
				break;
			case 'update':
				$order = $this->BlankOrder();

				$params = array(
					'Order' => $order,
				);

				return $this->renderWith(array('StandingOrdersPage_edit', 'Page'), $params);
				break;
			case 'load':
				$order = DataObject::get_one("Order", "UIDhash = '".$orderID."'");
				if($order) {
					$standingOrder = $order->StandingOrder();
					if($standingOrder) {
						$member = $order->Member();
						if(Member::currentUserID() != $member->ID && Member::currentUserID()){
							$oldMember = Member::currentUser();
							$oldMember->logOut();
						}
						if($member->ID != Member::currentUserID()) {
							$member->logIn();
						}
						if($member) {
							if($order->CompleteOrder() != $order->ID) {
								E_USER_ERROR("There was an error loading the Order", E_USER_ERROR);
							}
							Session::set('StandingOrder', null);
							Director::redirect(CheckoutPage::find_link());
						}
						else {
							USER_ERROR("Could not find the associated Standing Order.", E_USER_ERROR);
						}
					}
					else {
						USER_ERROR("Could not find member for order.", E_USER_ERROR);
					}
				}
				else {
					USER_ERROR("Could not find order.", E_USER_ERROR);
				}
				break;
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

					Director::redirect(StandingOrdersPage::get_standing_order_link("update", $orderID));

					$params = array();
				}
				else {
					$params = array(
						'StandingOrder' => false,
						'Message' => 'There is no order by that ID. You can <a href="' . $accountPageLink . '">edit your personal details and view your orders</a>.'
					);
				}

				return $this->renderWith(array('StandingOrdersPage_view', 'Page'), $params);
				break;
			case 'view':
				if($orderID) {
					if($order = DataObject::get_one('StandingOrder', "StandingOrder.ID = '$orderID' AND MemberID = '$memberID'")) {
						$params = array(
							'StandingOrder' => $order
						);
					} else {
						$params = array(
							'StandingOrder' => false,
							'Message' => 'You do not have any order corresponding to this ID. However, you can <a href="' . $accountPageLink . '">edit your personal details and view your orders.</a>.'
						);
					}
				} else {
					$params = array(
						'StandingOrder' => false,
						'Message' => 'There is no order by that ID. You can <a href="' . $accountPageLink . '">edit your personal details and view your orders.</a>.'
					);
				}
				return $this->renderWith(array('StandingOrdersPage_view', 'Page'), $params);

		}

	}

	public function StandingOrderForm() {
		$action = $this->urlParams['ID'];
		$orderID = $this->urlParams['OtherID'];

		if($action == 'create' || isset($_REQUEST['action_doCreate'])) {
			if(isset($_REQUEST['action_doCreate']) && isset($_REQUEST['OrderID'])) $orderID = $_REQUEST['OrderID'];
			return new StandingOrderForm($this, 'StandingOrderForm', $orderID);
		}
		else if($action == 'update' || isset($_REQUEST['action_doSave'])) {
			if(isset($_REQUEST['action_doSave']) && isset($_REQUEST['StandingOrderID'])) $orderID = $_REQUEST['StandingOrderID'];
			return new StandingOrderForm($this, 'StandingOrderForm', $orderID, true);
		}
	}

	public function BlankOrder() {
		//Create an Order to use
		$order = new Order();
		$MemberID = Member::currentUserID();
		if(!$MemberID) {
			E_USER_ERROR("Trying to create order without related user");
		}
		$order->MemberID = $MemberID;

		return $order;
	}

	function admin() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if(!Permission::check('ADMIN')) {
			return Security::permissionFailure($this, _t('OrderReport.PERMISSIONFAILURE', 'Sorry you do not have permission for this function. Please login as an Adminstrator'));
		}
		StandingOrder::create_automatically_created_orders();
		$params = array(
			"AllStandingOrders" => DataObject::get("StandingOrder", "{$bt}Status{$bt} = 'Active'")
		);
		Requirements::javascript("ecommerce_standingorders/javascript/StandingOrdersPage_admin.js");
		Requirements::themedCSS("StandingOrdersPage_admin");
		return $this->renderWith(array('StandingOrdersPage_admin', 'Page'), $params);

	}

}
