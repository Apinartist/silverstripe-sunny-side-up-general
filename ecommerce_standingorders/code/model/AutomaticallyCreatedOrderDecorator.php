<?php

/**
 * @author nicolaas @ sunnysideup . co . nz
 */

class AutomaticallyCreatedOrderDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				"UIDHash" => "Varchar(32)", //used to be able to open the order without username / password
				"OrderDate" => "Date", //date at which the order was instigated based on Standing Order Requirements
				"OrderDateInteger" => "Int" //date at which the order was instigated based on Standing Order Requirements INTEGER FOR easy lookup
			),
			'has_one' => array(
				'StandingOrder' => 'StandingOrder'
			),
			'casting' => array(
				"Completed" => "Boolean", //has been paid
				"OrderItemsAdded" => "Boolean" //has been paid
			),
			'indexes' => array(
				'UIDHash' => 'unique (UIDHash)'
			)
		);
	}

	/**
	 */
	public function Status() {
		if(!$this->owner->Completed()) {
			if(!$this->owner->OrderItemsAdded()){
				return 'Draft Order';
			}
			else {
				return "Items loaded but incomplete";
			}
		}
		return $this->owner->Status;
	}


	function Completed() {
		return DataObject::get_one("Payment", "OrderID =".$this->owner->ID, false);
	}

	function getCompleted(){
		return $this->owner->Completed();
	}

	function hasCompleted() {
		if($this->owner->Completed()) {return  true;} else {return false;}
	}

	function OrderItemsAdded() {
		return DataObject::get_one("OrderAttribute", "OrderID =".$this->owner->ID, false);
	}

	function getOrderItemsAdded(){
		return $this->owner->OrderItemsAdded();
	}

	function hasOrderItemsAdded() {
		if($this->owner->OrderItemsAdded()) {return  true;} else {return false;}
	}


	public function sendReceipt() {
		$this->owner->sendEmail('AutomaticallyCreatedOrderDecorator_ReceiptEmail');
	}




	/**
	 * Publish the order
	 * @return null
	 */
	public function publishOrder() {
		//$this->ClassName = 'Order';
		/*
		$modifiers = ShoppingCart::get_modifiers();

		if($modifiers) {
			 $this->createModifiers($modifiers, true);
		}

		$this->write();
		/* TO DO: implement....
		$paymentClass = $this->StandingOrder()->PaymentMethod;
		$payment = new $paymentClass();
		// Save payment data from form and process payment
		$payment->OrderID = $this->ID;
		$payment->Amount = $this->Total();
		$payment->write();

		// Process payment, get the result back
		$result = $payment->autoProcessPayment();
		if($result->isSuccess()) {
			$this->sendReceipt();
		}
		*/

	}

	function ViewHTMLLink() {
		return '<a href="OrderReportWithLog_Popup/invoice/'.$this->owner->ID.'" class="makeIntoPopUp">View</a>';
	}

	function LoadHTMLLink() {
		return '<a href="'.$this->LoadLink().'" class="makeIntoPopUp">Load</a>';
	}

	function LoadLink() {
		return StandingOrdersPage::get_standing_order_link("load", $this->owner->UIDHash);
	}

	function FuturePast() {
		$currentTime = strtotime(Date("Y-m-d"));
		$orderTime = strtotime($this->owner->OrderDate);
		if($currentTime > $orderTime) {
			return "past";
		}
		elseif($currentTime == $orderTime) {
			return "current";
		}
		else {
			return "future";
		}
	}

	function CompleteOrder() {
		ShoppingCart::clear();
		if($standingOrder = $this->owner->StandingOrder()) {
			// Set the items from the cart into the order
			if($standingOrder->OrderItems()) {
				foreach($standingOrder->OrderItems() as $orderItem) {
					$product = DataObject::get_by_id('Product', $orderItem->ProductID);
					if($product) {
						if(class_exists("ProductStockCalculatedQuantity")) {
							$numberAvailable = ProductStockCalculatedQuantity::get_quantity_by_product_id($product->ID);
							if($numberAvailable < $orderItem->Quantity) {
								$dos = $standingOrder->AlternativesPerProduct($product->ID);
								$product = null;
								if($dos) {
									foreach($dos as $do) {
										$numberAvailable = ProductStockCalculatedQuantity::get_quantity_by_product_id($do->ID);
										if($numberAvailable > $orderItem->Quantity) {
											$product = $do;
										}
									}
								}
							}
						}
						if($product) {
							ShoppingCart::add_new_item(
								new Product_OrderItem(
									array(
										'ProductID' => $orderItem->ProductID,
										'ProductVersion' => $orderItem->ProductVersion(),
										'Quantity' => $orderItem->Quantity,
									),
									$orderItem->Quantity
								)
							);
						}
					}
					else {
						USER_ERROR("Product does not exist", E_USER_WARNING);
					}
				}
			}
			else {
				USER_ERROR("There are no order items", E_USER_WARNING);
			}

		}
		else {
			USER_ERROR("Order #".$this->owner->ID." does not have a standing order associated with it!", E_USER_WARNING);
		}
		Session::set("DraftOrderID", $this->owner->ID);
		return Session::get("DraftOrderID");
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if($id = Session::get("DraftOrderID")) {
			echo "OK 1";
			$oldOne = DataObject::get_by_id("Order", $id);
			if($oldOne && $this->owner->MemberID = $oldOne->MemberID) {
				echo "OK 2";
				$this->owner->OrderDate = $oldOne->OrderDate;
				$this->owner->OrderDateInteger = $oldOne->OrderDateInteger;
				$this->owner->UIDHash = $oldOne->UIDHash;
				$this->owner->StandingOrderID = $oldOne->StandingOrderID;
				$oldOne->delete();
			}
			else {
				USER_ERROR("Originating Order not correct", E_USER_NOTICE);
			}
		}
		Session::set("DraftOrderID", null);
		if(!strlen($this->owner->UIDHash) == 32) {
			$this->owner->UIDHash = substr(base_convert(md5(uniqid(mt_rand(), true)), 16, 36),0, 32);
		}
		$this->owner->OrderDateInteger = strtotime($this->owner->OrderDate);
	}

	function onAfterWrite() {
		parent::onAfterWrite();
	}

}

class AutomaticallyCreatedOrderDecorator_ReceiptEmail extends Email {

	protected $ss_template = 'AutomaticallyCreatedOrderDecorator_ReceiptEmail';

}