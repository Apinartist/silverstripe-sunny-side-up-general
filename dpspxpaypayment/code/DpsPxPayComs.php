<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/

class DpsPxPayComs extends object {

	/**
	* seller details - are always the same
	* can be set like this DpsPxPayComs::set_pxpay_userid("blabla"); in _config file.
	**/
  protected static $pxpay_url    = "https://www.paymentexpress.com/pxpay/pxaccess.aspx";
		static function set_pxpay_url($v) { self::$pxpay_url = $v;}
	protected static $pxpay_userid = "UserId";
		static function set_pxpay_userid($v) { self::$pxpay_userid = $v;}
	protected static $pxpay_encryption_key    =  "Encryptionkey";
		static function set_pxpay_encryption_key($v) { self::$pxpay_encryption_key = $v;}
	protected static $alternative_thirdparty_folder    =  "";
		static function set_alternative_thirdparty_folder($v) { self::$alternative_thirdparty_folder = $v;}

	/**
	* customer details
	**/
	protected $AmountInput = 0;
		public function setAmountInput($v)  { $this->AmountInput = $v;}
	protected $TxnData1 = "";
		public function setTxnData1($v)     { $this->TxnData1 = $v;}
	protected $TxnData2 = "";
		public function setTxnData2($v)     { $this->TxnData2 = $v;}
	protected $TxnData3 = "";
		public function setTxnData3($v)     { $this->TxnData3 = $v;}
	protected $TxnType = "Purchase";
		public function setTxnType($v)      { $this->TxnType = $v;}
	protected $EmailAddress = "";
		public function setEmailAddress($v) { $this->EmailAddress = $v;}

	/**
	* order details
	**/
	protected $MerchantReference = "";
		public function setMerchantReference($v) { $this->MerchantReference = $v;}
	protected $AmountInput = 0;
		public function setAmountInput($v)       { $this->AmountInput = $v;}
	protected $CurrencyInput = "NZD";
		public function setCurrencyInput($v)     { $this->CurrencyInput = $v;}

	/**
	* details of the redirection
	**/
	protected $setUrlFail = "";
		public function setUrlFail($v)           { $this->setUrlFail = $v;}
	protected $setUrlSuccess = "";
		public function setUrlSuccess($v)        { $this->UrlSuccess = $v;}

	/**
	* external object
	**/
	protected $PxPayObject = null;

	function __construct() {
		if(self::$alternative_thirdparty_folder) {
			require_once(self::$alternative_thirdparty_folder."/PxPay_Curl.inc.php");
		}
		else {
			require_once("dpspxpaypayment/code/thirdparty/PxPay_Curl.inc.php");
		}
		if(!self::$pxpay_url)            {user_error("error in ".$this->ClassName."::__construct, self::$pxpay_url not set. ", E_USER_WARNING);}
		if(!self::$pxpay_userid)         {user_error("error in ".$this->ClassName."::__construct, self::$pxpay_userid not set. ", E_USER_WARNING);}
		if(!self::$pxpay_encryption_key) {user_error("error in ".$this->ClassName."::__construct, self::$pxpay_encryption_key not set. ", E_USER_WARNING);}
	  $PxPayObject = new PxPay_Curl( self::$pxpay_url, self::$pxpay_userid, self::$pxpay_encryption_key);
	}

	/*
	 * This function receives information back from the payments page as a response object
	 * --------------------- RESPONSE DATA ---------------------
	 * $Success           = $resonseObject->getSuccess();   # =1 when request succeeds
	 * $AmountSettlement  = $resonseObject->getAmountSettlement();
	 * $AuthCode          = $resonseObject->getAuthCode();  # from bank
	 * $CardName          = $resonseObject->getCardName();  # e.g. "Visa"
	 * $CardNumber        = $resonseObject->getCardNumber(); # Truncated card number
	 * $DateExpiry        = $resonseObject->getDateExpiry(); # in mmyy format
	 * $DpsBillingId      = $resonseObject->getDpsBillingId();
	 * $BillingId         = $resonseObject->getBillingId();
	 * $CardHolderName    = $resonseObject->getCardHolderName();
	 * $DpsTxnRef	        = $resonseObject->getDpsTxnRef();
	 * $TxnType           = $resonseObject->getTxnType();
	 * $TxnData1          = $resonseObject->getTxnData1();
	 * $TxnData2          = $resonseObject->getTxnData2();
	 * $TxnData3          = $resonseObject->getTxnData3();
	 * $CurrencySettlement= $resonseObject->getCurrencySettlement();
	 * $ClientInfo        = $resonseObject->getClientInfo(); # The IP address of the user who submitted the transaction
	 * $TxnId             = $resonseObject->getTxnId();
	 * $CurrencyInput     = $resonseObject->getCurrencyInput();
	 * $EmailAddress      = $resonseObject->getEmailAddress();
	 * $MerchantReference = $resonseObject->getMerchantReference();
	 * $ResponseText      = $resonseObject->getResponseText();
	 * $TxnMac            = $resonseObject->getTxnMac(); # An indication as to the uniqueness of a card used in relation to others
	 **/

	function processRequestAndReturnResultsAsObject() {
		$enc_hex = $_REQUEST["result"];
		#getResponse method in PxPay object returns PxPayResponse object
		#which encapsulates all the response data
		return $this->PxPayObject->getResponse($enc_hex);
	}

	/*
	 * This function formats data into a request and redirects to the Payments Page.
	 * NOTE: you will need to set all the variables prior to running this.
	 * e.g. $myDPSPxPayComsObject->setMerchantReference("myreferenceHere");
	 **/
	function startPaymentProcess() {

		$request = new PxPayRequest();

		#Set PxPay properties
		if($this->MerchantReference) {$request->setMerchantReference($this->MerchantReference);}  else { user_error("error in ".$this->ClassName."::startPaymentProcess, MerchantReference not set. ", E_USER_WARNING);}
		if($this->AmountInput)       {$request->setAmountInput($this->AmountInput);}              else { user_error("error in ".$this->ClassName."::startPaymentProcess, AmountInput not set. ", E_USER_WARNING);}
		if($this->Address1)          {$request->setTxnData1($this->Address1);}                    else { user_error("error in ".$this->ClassName."::startPaymentProcess, Address1 not set. ", E_USER_WARNING);}
		if($this->Address2)          {$request->setTxnData2($this->Address2);}                    else { user_error("error in ".$this->ClassName."::startPaymentProcess, Address2 not set. ", E_USER_WARNING);}
		if($this->Address3)          {$request->setTxnData3($this->Address3);}                    else { user_error("error in ".$this->ClassName."::startPaymentProcess, Address3 not set. ", E_USER_WARNING);}
		if($this->TxnType)           {$request->setTxnType($this->TxnType);}                      else { user_error("error in ".$this->ClassName."::startPaymentProcess, TxnType not set. ", E_USER_WARNING);}
		if($this->CurrencyInput)     {$request->setCurrencyInput($this->CurrencyInput);}          else { user_error("error in ".$this->ClassName."::startPaymentProcess, CurrencyInput not set. ", E_USER_WARNING);}
		if($this->EmailAddress)      {$request->setEmailAddress($this->EmailAddress;}             else { user_error("error in ".$this->ClassName."::startPaymentProcess, EmailAddress not set. ", E_USER_WARNING);}
		if($this->UrlFail)           {$request->setUrlFail($this->UrlFail);	}                     else { user_error("error in ".$this->ClassName."::startPaymentProcess, UrlFail not set. ", E_USER_WARNING);}
		if($this->UrlSuccess)        {$request->setUrlSuccess($this->UrlSuccess);}                else { user_error("error in ".$this->ClassName."::startPaymentProcess, UrlSuccess not set. ", E_USER_WARNING);}
		$request->setTxnId(uniqid("ID"));

		#The following properties are not used in this case
		$request->setEnableAddBillCard($EnableAddBillCard);
		$request->setBillingId($BillingId);
		$request->setOpt($Opt);

		#Call makeRequest function to obtain input XML
		$request_string = $this->PxPayObject->makeRequest($request);

		#Obtain output XML
		$response = new MifMessage($request_string);

		#Parse output XML
		$url = $response->get_element_text("URI");
		$valid = $response->get_attribute("valid");

		 #Redirect to payment page
		 header("Location: ".$url);
	}

	function debug() {
		debug::show("debugging ".$this->ClassName);
		print_r($this);
		print_r($this->PxPayObject);
	}
}