<?php

/**
 * @todo getPaymentFormFields : SPTInfo need to display the Paymark logo if merchant approved rather than the SPT logo below
 * @package ecommerce
 */

/**
 * Sub-class of Payment that supports SecurePayTech as its payment processor
 **/

/**
 *  Configuration
 *  =============
 *  You need to define the installation ID, test mode and callback
 *  password in _config.php of your project:
 */
class SecurePayTechPayment extends Payment {

	protected static $spt_supported_cards;

	protected static $spt_soap_client = "https://tx.securepaytech.com:8443/web/SoapPurchase?wsdl";
		static function set_spt_soap_client($spt_soap_client) {self::$spt_soap_client = $spt_soap_client;}

	protected static $spt_soap_uri = 'http://tx.securepaytech.com';
		static function set_spt_soap_uri($spt_soap_uri) {self::$spt_soap_uri = $spt_soap_uri;}

	protected static $spt_merchant_id;
		static function set_spt_merchant_id($spt_merchant_id) {self::$spt_merchant_id = $spt_merchant_id;}

	protected static $spt_merchant_key;
		static function set_spt_merchant_key($spt_merchant_key) {self::$spt_merchant_key = $spt_merchant_key;}

	protected static $spt_merchant_name;
		static function set_spt_merchant_name($spt_merchant_name) {self::$spt_merchant_name = $spt_merchant_name;}

	protected static 	$spt_merchant_address;
		static function set_spt_merchant_address($spt_merchant_address) {self::$spt_merchant_address = $spt_merchant_address;}


	protected static $credit_cards = array(
		'Visa' => 'ecommerce/images/payments/visa.jpg',
		'MasterCard' => 'ecommerce/images/payments/mastercard.jpg',
		'Amex' => 'ecommerce/images/payments/methods/american-express.gif',
		'Diners' => 'ecommerce/images/payments/methods/dinners-club.jpg',
		'JCB' => 'ecommerce/images/payments/methods/jcb.jpg'
	);

	static function remove_credit_card($creditCard) {unset(self::$credit_cards[$creditCard]);}

	/**
	 * This function processes the payment via the SPT communication object
	 */
	function processPayment($data, $form) {

		$details = array(
			'CardHolderName' => $this->CardHolderName,
			'CreditCardNumber' => $this->CreditCardNumber,
			'Amount' => $this->Amount,
			'InputCurrency' => $this->InputCurrency,
			'CardType' => "1",
			'DateExpiry' => $this->CreditCardExpiry,
			'TxnType' => $this->ManuallyConfirm ? "Auth" : "Purchase",
			'MerchantReference' => "Order".$this->OrderID,
			"EnableDuplicateCheck" => "0",
		);
		if($this->BillingId){
			$details["EnableRm"] = "1";
			$details["SptTxnRef"] = $this->BillingId;
		}

		$settings = array(
			'SoapClient' => self::$spt_soap_client,
			'SoapURI' => self::$spt_soap_uri,
			'MerchantID' => self::$spt_merchant_id,
			'MerchantKey' => self::$spt_merchant_key,
			'SupportedCards' => self::$spt_supported_cards,
			'MerchantName' => self::$spt_merchant_name,
			'MerchantAddress' => self::$spt_merchant_address,
		);

		// This allows $data to be passed directly from the form.
		if(is_array($details['CreditCardNumber'])){
			$details['CreditCardNumber'] = implode("",$this->CreditCardNumber);
		}
		$result = SecurePayTechPayment_Communicator::txcall($details, $settings);
		$this->Message = "<p>Merchant ID: $settings[MerchantID]<br /> $result[ResponseText] <br /> $result[HelpText]</p>";
		if($result['Fatal'] ){
			// Server of communication problems
			$e = new Email(
				"nfrancken@gmail.com",
				"nfrancken@gmail.com",
				"SPT FATAL ERROR",
				"<h1> Fatal Error on " . $project ." </h1> <p> <strong> SPT Response: </strong>$result[ResponseText]
				 <br /> <strong>Helptext:</strong> $result[HelpText] </p> $result[MerchantHelpText]</p>"
			);
			$e->send();
		}
		if($result['Success']) {
			$this->Status = "Success";
			$order = $this->Order();
			$order->Status = 'Paid';
			$order->write();

			// create a log-entry for it
		$logEntry = new OrderStatusLog();
		$logEntry->OrderID = $order->ID;
		$logEntry->Status = 'Paid';
		$logEntry->write();
		}
		else {
			$this->Status = "Failure";
			Session::set('Message', $result['HelpText']);
		}
		$this->write();
		return $result;
	}


	/**
	 * Create the required form fields with all the info we need to process the transaction
	 */

	function getPaymentFormFields() {
		$site_currency = Payment::site_currency();
		$paymentsList = '<div style="vertical-align: middle;">';
		foreach(self::$credit_cards as $name => $image) {
			$paymentsList .= '<img src="' . $image . '" alt="' . $name . '"/>';
		}
		$paymentsList.='<img src="ecommerce/images/payments/paymark.png" alt="Paymark Certified" onclick="paymarkVerify(\''.self::$spt_merchant_id.'\')"/></div>';
		return new FieldSet(
			new TextField("CardHolderName", "Card Holder Name:"),
			new CreditCardField("CreditCardNumber", "Credit Card Number:"),
			new NumericField("CreditCardExpiry", "Credit Card Expiry:(MMYY)", "", 4),
			new LiteralField("SPTInfo", $paymentsList), //'<a href="http://www.securepaytech.com/" title="SecurePayTech"><img src="https://merchant.securepaytech.com/images/spt-logo.png" alt="Payments powered by SecurePayTech" border="0" /></a>'),
			new HiddenField("InputCurrency", "InputCurrency", $site_currency)
		);
	}

	/**
	 * Returns the required fields to add to the order form, when using this payment method.
	 */
	function getPaymentFormRequirements() {
		return array(
			"js" => "
				require('CardHolderName');
				require('CreditCardNumber');
				require('CreditCardExpiry');
			",
			"php" => '
				$this->requireField("CardHolderName", $data);
				$this->requireField("CreditCardNumber", $data);
				$this->requireField("CreditCardExpiry", $data);
				$this->requireField("InputCurrency", $data);
			',
		);
	}


	/*
	 * Returns the Cheque content from the CheckoutPage
	 */
	function ChequeContent() {
		return DataObject::get_one('CheckoutPage')->ChequeMessage;
	}

}



class SecurePayTechPayment_Communicator{

	/**
	 * Required XML fields for Px POST
	 *  Auth:  Authorizes a transactions. Must be completed within 7 days using the "Complete" TxnType.
	 *  Purchase:  Funds are transferred immediately
	 *  Complete:  Completes (settles) a pre-approved Auth Transaction. The SptTxnRef value returned by the original approved Auth transaction must be supplied.
	 *  Refund: - Funds transferred immediately. Must be enabled as a special option.
	 */
	 protected static $requiredDetails = array(
		"Auth" => array("MerchantReference", "Amount", "InputCurrency", "CardType", "CreditCardNumber", "DateExpiry", "CardHolderName"),
		"AuthCSC" => array("MerchantReference", "Amount", "InputCurrency", "CardType", "CreditCardNumber", "DateExpiry", "CardHolderName", "CSC"),
		"Purchase" => array("MerchantReference", "Amount", "InputCurrency", "CardType", "CreditCardNumber", "DateExpiry", "CardHolderName"),
		"PurchaseCSC" => array("MerchantReference", "Amount", "InputCurrency", "CardType", "CreditCardNumber", "DateExpiry", "CardHolderName", "CSC"),
		"Complete" => array("Amount", "SptTxnRef"),
		"Refund" => array("Amount", "SptTxnRef"),
	);

	/**
	 * Valid elements for SOAP Purchase
	 */
	protected static $legal_details = array(
		// Required
		"MerchantReference",	// Optional Reference to Appear on Transaction Reports Max 64 Characters
		"Amount", 				// Amount of transaction (dddddd.cc)
		"InputCurrency", 		// Currency
		"CardType", 			//  Card type (Visa, Mastercard, etc)
		"CreditCardNumber",
		"DateExpiry", 			//  Expiry Date on Card
		"CardHolderName",
		// Optional
		"CSC", 					// Card Verification number (CVV2 or CSC). This number is found on the back of a credit card in the signature panel - it is different from the embossed card number and provides an additional safety check.
		"SptTxnRef",			// Output from an original SPT transaction request. Is a required field to do second stage transactions like refund and completions.
	);

	/**
	 * Supported Currencies
	 * At the moment, SPT only supports payments in NZD
	 */

	protected static $supported_currencies = array(
		"NZD"  => "New Zealand Dollar",
	);

	static function txcall($details, $settings) {
		// Allowed detail entries

		// Check settings configured
		if(!$settings[MerchantID] || !$settings[MerchantKey] || !$settings[SoapClient] || !$settings[SoapURI]) {
			user_error("SPT Module incorrectly configured [1]", E_USER_ERROR);

			return array(
				"Success" => false,
				"Fatal" => true,
				"ResponseText" => "MISSING_SETTINGS",
				"HelpText" => "The server has been misconfigured",
				"MerchantHelpText" => "SPT Module incorrectly configured [2]",
			);
		}else{
		// All ok, we can proceed and transaction type to settings
		$settings[TxnType] = $details[TxnType];

		}

		// Check required details
		$requiredDetails = self::$requiredDetails;

		foreach($requiredDetails[$details[TxnType]] as $k) {
			if(!$details[$k]) {
				return array(
					"Success" => false,
					"Fatal" => true,
					"ResponseText" => "MISSING_DETAILS",
					"HelpText" => "The server has been misconfigured",
					"MerchantHelpText" => "sptRequest_txd not required detail '$k'",
				);
			}
		}

		$supported_currencies = self::$supported_currencies;
		if($details[InputCurrency])
			if( !array_key_exists("$details[InputCurrency]",$supported_currencies) )
				USER_ERROR("SPT: Unsupported currency $details[InputCurrency]",E_USER_ERROR);


		// Build transaction SOAP Call array
		$transactionCall = array();

		// We add in all the required variables
		foreach($details as $k => $v) {
			// Illegal details are ignored
			if(in_array($k, self::$legal_details)) {
				$v = htmlentities($v);
				$transactionCall[$k] = $v;
			}
		}


	 	// process the transaction, sending SOAP call to SPT servers
	 	$response = SecurePayTechPayment_Communicator::postTransaction($settings, $transactionCall);

		if(!$response)
			return array(
				"Success" => 0,
				"ReCo" => "X1",
				"ResponseText" => "COMMUNICATIONS ERROR",
				"HelpText" => "There has been a communication error with the payment server",
			);


		$success = $response->resultCode;

		if($success === false) {


					// This situation below is the final (worst-case) situation.
					mail("nfrancken@gmail.com", "URGENT CREDIT CARD ERROR!",
						"The following response could not be decoded:\n\n$statusResponse");
					user_error("I'm sorry, but our credit card server is not currently working.  We will let you
							know by e-mail as soon as it is back up and running",E_USER_ERROR);

		}

		$reco           = "";
		$responsetext   = $response->failReason;
		$helptext       = "";
		$datesettlement = "";
		if(ereg('([0-9]{4})([0-9]{2})([0-9]{2})',$datesettlement,$parts))
			$datesettlement = "$parts[1]-$parts[2]-$parts[3]";
		$txnref = $response->merchTxnRef;

		$returnVal = array(
			"ReCo" => $reco,
			"ResponseText" => $responsetext,
			"TxnRef" => "Transaction Reference: " . $txnref,

			"CardHolderResponseText" => "CardHolderResponseText",
			"ResponseDescription" => "ResponseDescription",
			"MerchantResponseText" => $response->failReason,
			"MerchantResponseDescription" => $response->failReason,

		);

		// Build merchant name, address etc as required on SPT payment pages
		$merchantDetails = "<br /> <br /> <strong>" . $settings[MerchantName]
							. "</strong><br />" . $settings[MerchantAddress]
							. "<br />Merchant ID: " . $settings[MerchantID]
							. "<br /> <br /> Date/Time: " . date('d-M-Y H:i:s')
							. "<br /> <br />Order Reference: " . $details[MerchantReference]
							. "<br />MercnTxnRef: " . $txnref
							. "<br />Receipt No: " . $response->receiptNo .
							 "<br /> <br />Purchase Amount: " . $details[InputCurrency] . " " . $details[Amount] ;


		// Now we add in appropriate help text based on success code
		// See SecurePayTech docs for list of statuses and descriptions

		switch ($success) {
			case 1:
				$returnVal['Success'] = 1;
				$returnVal["HelpText"] = '<strong>Result: ACCEPTED</strong><br />' . $merchantDetails;
				break;
			case 2:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Insufficient Funds ' . $merchantDetails;
				break;
			case 3:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Card Expired ' . $merchantDetails;
				break;
			case 4:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Card Declined ' . $merchantDetails;
				break;
			case 5:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Server Error: '.$results[1];
				break;
			case 6:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Communications Error: '.$results[1];
				break;
			case 7:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Unsupported Transaction Type' . $merchantDetails;
				break;
			case 8:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Bad Request: Check that details have been entered correctly' . $merchantDetails;
				break;
			case 9:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'Invalid Card Number' . $merchantDetails;
				break;
			default:
				$returnVal['Success'] = 0;
				$returnVal["HelpText"] = 'Result: DECLINED<br /> Reason';
				$returnVal["MerchantHelpText"]= 'An error has occurred: Please contact us for assistance.' . $merchantDetails;
		}

		return $returnVal;
	}

	/*
	 * Sends transaction call to SPT via SOAP and retrieves response
	 *
	 */
	private static function postTransaction($settings, $data) {

	// We start up the call to the SecurePayTech soap client, using the WSDL method
	$client = new SoapClient($settings[SoapClient], array(
												 'uri' => $settings[SoapURI],
												 'use' => SOAP_ENCODED,
												 'style' => SOAP_RPC));

			$merchantID = $settings[MerchantID];
			$merchantKey = $settings[MerchantKey];
			$orderRef = $data[MerchantReference];
			$amount = $data[Amount];
			$currency = $data[InputCurrency];
			$cardType = $data[CardType];
			$cardNumber = $data[CreditCardNumber];
			$cardExpiry = $data[DateExpiry];
			$cardHolder = $data[CardHolderName];
			$csc = $data[CSC];


	try {
	//Perform the SOAP method call
	if($settings[TxnType]=="Purchase"){
				$result = $client->purchase($merchantID,$merchantKey,$orderRef, $amount,$currency,$cardType,$cardNumber,$cardExpiry,$cardHolder);
			}elseif($settings[TxnType]=="PurchaseCSC"){
				$result = $client->purchaseWithCsc($merchantID,$merchantKey,$orderRef, $amount,$currency,$cardType,$cardNumber,$cardExpiry,$cardHolder,$csc);
			}else{
				$result = $client->purchasea($settings[MerchantID] . "," . $settings[MerchantKey] . "," . $data[MerchantReference] . "," . $data[Amount] . "," . $data[InputCurrency] . "," . $data[CardType] . "," . $data[CreditCardNumber] . "," . $data[DateExpiry] . "," . $data[CardHolderName]);
			}

		} catch (SoapFault $f) {
			$error = $f->getMessage();
			$error .= "<br />";
			$error .= $f->faultcode;
			$error .= "<br />";
			$error .= $f->faultstring;
			$error .= "<br />";
			//print_r($f->getTrace());
		}


		return $result;
	}
}









