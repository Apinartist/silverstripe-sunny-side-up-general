<?php

/**
 * Payment type to support credit-card payments through DPS.
 *
 * Supported currencies:
 * 	CAD  	Canadian Dollar
 * 	CHF 	Swiss Franc
 * 	EUR 	Euro
 * 	FRF 	French Franc
 * 	GBP 	United Kingdom Pound
 * 	HKD 	Hong Kong Dollar
 * 	JPY 	Japanese Yen
 * 	NZD 	New Zealand Dollar
 * 	SGD 	Singapore Dollar
 * 	USD 	United States Dollar
 * 	ZAR 	Rand
 * 	AUD 	Australian Dollar
 * 	WST 	Samoan Tala
 * 	VUV 	Vanuatu Vatu
 * 	TOP 	Tongan Pa'anga
 * 	SBD 	Solomon Islands Dollar
 * 	PGK 	Papua New Guinea Kina
 * 	MYR 	Malaysian Ringgit
 * 	KWD 	Kuwaiti Dinar
 * 	FJD 	Fiji Dollar
 *
 * @package payment
 *
 * unique identifier: OrderNumber and PaymentID
 *
 */

class DpsPxPayPayment extends Payment {

	static $db = array(
		'TxnRef' => 'Text'
	);

	// DPS Information

	protected static $privacy_link = 'http://www.paymentexpress.com/privacypolicy.htm';

	protected static $logo = 'payment/images/payments/dps.gif';

	// URLs

	protected static $url = 'https://www.paymentexpress.com/pxpost.aspx';

	protected static $credit_cards = array(
		'Visa' => 'payment/images/payments/methods/visa.jpg',
		'MasterCard' => 'payment/images/payments/methods/mastercard.jpg',
		'American Express' => 'payment/images/payments/methods/american-express.gif',
		'Dinners Club' => 'payment/images/payments/methods/dinners-club.jpg',
		'JCB' => 'payment/images/payments/methods/jcb.jpg'
	);

	static function remove_credit_card($creditCard) {
		unset(self::$credit_cards[$creditCard]);
	}

	function getPaymentFormFields() {
		$logo = '<img src="' . self::$logo . '" alt="Credit card payments powered by DPS"/>';
		$privacyLink = '<a href="' . self::$privacy_link . '" target="_blank" title="Read DPS\'s privacy policy">' . $logo . '</a><br/>';
		$paymentsList = '';
		foreach(self::$credit_cards as $name => $image) {
			$paymentsList .= '<img src="' . $image . '" alt="' . $name . '"/>';
		}
		$fields = new FieldSet(
			new LiteralField('DPSInfo', $privacyLink),
			new LiteralField('DPSPaymentsList', $paymentsList),
		);
		return $fields;
	}


	function processPayment($data, $form) {
		$this->
		// 2) Payment Informations
		$commsObject = new DpsPxPayComs();

		/**
		* order details
		**/
		$commsObject->setTxnType('Purchase');
		$commsObject->setMerchantReference($v);
		$commsObject->setAmountInput($v);
		$commsObject->setCurrencyInput($this->Currency);
		/**
		* details of the redirection
		**/
		$commsObject->setUrlFail($v);
		$commsObject->setUrlSuccess($v);

		//redirect
		$commsObject->startPaymentProcess();
		return;
	}


}

class DpsPxPayPayment_Handler extends Controller {

	static $url_segment = 'dpspxpaypayment';

	static function complete_link() {
		return self::$url_segment . '/complete';
	}

	/**
	 * Get the Order object to modify, check security that it's the object you want to modify based
	 * off Worldpay confirmation, update the Order object to show complete and Payment object to show
	 * that it was received. Finally, send a receipt to the buyer to show these details.
	 */
	function paid() {

		$commsObject = new DpsPxPayComs();
		$response = $commsObject->processRequestAndReturnResultsAsObject();
		if($_REQUEST['callbackPW'] == WorldpayPayment::$callback_password) {
			$paymentID = $_REQUEST['MC_paymentID'];
			if(is_numeric($paymentID)) {
				if($payment = DataObject::get_by_id('WorldpayPayment', $paymentID)) {
					if($_REQUEST['transStatus'] == "Y")	$payment->Status = 'Success';
					else $payment->Status = 'Failure';
					$paymentID = $_REQUEST['MC_paymentID'];
					$payment = DataObject::get_by_id('WorldpayPayment', $paymentID);
					if(1 == $response->getSuccess() ) {
						$payment->Status = 'Success';
					}
					else {
						$payment->Status = 'Failure';
					}
					if($DpsTxnRef = $response->getDpsTxnRef()) $this->TxnRef = $DpsTxnRef;
					if($ResponseText = $response->getResponseText()) $this->Message = $ResponseText;
					$payment->write();
					$payment->redirectToOrder();
				}
				else USER_ERROR("CheckoutPage::OrderConfirmed - There is no Payment object for this order object (Order ID ".$orderID.")", E_USER_WARNING);
			}
			else USER_ERROR('CheckoutPage::OrderConfirmed - Order ID is NOT numeric', E_USER_WARNING);
		}
		else USER_ERROR("CheckoutPage::OrderConfirmed - Order error - password failed" ,E_USER_WARNING);
		return;
	}

	function complete() {

	}

}
