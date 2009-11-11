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

	protected static $logo = 'dpspxpaypayment/images/dps_paymentexpress_small.png';

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
			new LiteralField('DPSPaymentsList', $paymentsList)
		);
		return $fields;
	}

	function getPaymentFormRequirements() {
		return array();
	}
	function processPayment($data, $form) {
		$commsObject = new DpsPxPayComs();

		/**
		* order details
		**/
		$commsObject->setTxnType('Purchase');
		$commsObject->setMerchantReference($this->ID);
		//replace any character that is NOT [0-9] or dot (.)
		$commsObject->setAmountInput(floatval(preg_replace("/[^0-9\.]/", "", $data["Amount"])));
		$commsObject->setCurrencyInput($this->Currency);
		/**
		* details of the redirection
		**/
		$commsObject->setUrlFail(DpsPxPayPayment_Handler::absolute_complete_link());
		$commsObject->setUrlSuccess(DpsPxPayPayment_Handler::absolute_complete_link());

		/**
		* process payment data (check if it is OK and go forward if it is...
		**/
		$url = urlencode($commsObject->startPaymentProcess());

		if($url) {
			/**
			* build redirection page
			**/
			$page = new Page();
			$page->Title = 'Redirection to DPS...';
			$page->Logo = '<img src="' . self::$logo . '" alt="Payments powered by DPS"/>';
			$page->Form = $this->DPSForm($url);
			$controller = new ContentController($page);
			Requirements::clear();
			Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
			return new Payment_Processing($controller->renderWith('PaymentProcessingPage'));
		}
		else {
			$page = new Page();
			$page->Title = 'Sorry, DPS can not be contacted at the moment ...';
			$page->Logo = 'Sorry, an error has occured in contacting the Payment Processing Provider, please try again in a few minutes...';
			$page->Form = $this->DPSForm($url);
			$controller = new ContentController($page);
			Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
			return new Payment_Failure($controller->renderWith('PaymentProcessingPage'));
		}
	}

	function DPSForm($url) {
		return <<<HTML
			<form id="PaymentForm" method="post" action="$url"></form>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					var url = jQuery("#PaymentForm").attr("action");
					window.location(url);
				});
			</script>
HTML;
	}

}

class DpsPxPayPayment_Handler extends Controller {

	static $url_segment = 'dpspxpaypayment';

	static function complete_link() {
		return self::$url_segment . '/paid/';
	}

	static function absolute_complete_link() {
		return Director::AbsoluteURL(self::complete_link());
	}

	function paid() {
		$commsObject = new DpsPxPayComs();
		$response = $commsObject->processRequestAndReturnResultsAsObject();
		if($payment = DataObject::get_by_id('DpsPxPayPayment', $response->getMerchantReference())) {
			if(1 == $response->getSuccess()) {
				$payment->Status = 'Success';
			}
			else {
				$payment->Status = 'Failure';
			}
			if($DpsTxnRef = $response->getDpsTxnRef()) $payment->TxnRef = $DpsTxnRef;
			if($ResponseText = $response->getResponseText()) $payment->Message = $ResponseText;
			$payment->write();
			$payment->redirectToOrder();
		}
		else {
			USER_ERROR("could not find payment with matching ID", E_USER_WARNING);
		}
		return;
	}


}
