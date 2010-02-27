<?php

/**
 * @author Michael Mitchell <michael@sunnysideup.co.nz>
 */

class DraftOrder extends Order {

	public static $db = array(
		"UIDHash" => "Varchar(32)"
	);

	public static $has_one = array(
		'StandingOrder' => 'StandingOrder',
	);

	static $indexes = array(
		'UIDHash' => 'unique (UIDHash)'
	);

	/**
	 * Always return "Draft Order" as the status
	 */
	public function Status() {
		return 'Draft Order';
	}

	public function sendReceipt() {
		$this->sendEmail('DraftOrder_ReceiptEmail');
	}

	/**
	 * Publish the order
	 * @return null
	 */
	public function publishOrder() {

		$relatedStandingOrder = $this->StandingOrder();

		$this->ClassName = 'Order';

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

	function ViewLink() {
		return '<a href="OrderReportWithLog_Popup/invoice/'.$this->ID.'" class="makeIntoPopUp">View</a>';
	}

	function LoadLink() {
		return '<a href="'.StandingOrdersPage::get_standing_order_link("load", $this->ID).'" class="makeIntoPopUp">Load</a>';
	}


	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->UIDHash = substr(base_convert(md5(uniqid(mt_rand(), true)), 16, 36),0, 32);
	}

}

class DraftOrder_ReceiptEmail extends Email {

	protected $ss_template = 'DraftOrder_ReceiptEmail';

}