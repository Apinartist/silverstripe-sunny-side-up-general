<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * Sub-class of Payment that supports SecurePayTech as its payment processor
 *
 * Note: You must have the cURL extension installed in PHP in order to use
 * this class
 **/

/**
 *  Configuration
 *  =============
 *  You need to define the merchant id and key in the _config.php of your
 *  project:
 */
class SecurePayTechPayment extends Payment {

  protected static $credit_cards = array(
    'Visa' => 'payment/images/payments/methods/visa.jpg',
    'MasterCard' => 'payment/images/payments/methods/mastercard.jpg',
    //These two are usually not supported
    //'Amex' => 'payment/images/payments/methods/american-express.gif',
    //'Diners' => 'payment/images/payments/methods/dinners-club.jpg',
  );

  protected static $spt_merchant_id;

  static function set_spt_merchant_id ($spt_merchant_id) {self::$spt_merchant_id = $spt_merchant_id;}

  protected static $spt_merchant_key;

  static function set_spt_merchant_key($spt_merchant_key) {self::$spt_merchant_key = $spt_merchant_key;}


  function getPaymentFormFields() {
    $site_currency = Payment::site_currency();
    $paymentsList = '<div style="vertical-align: middle;">';
    foreach(self::$credit_cards as $name => $image) {
      $paymentsList .= '<img src="' . $image . '" alt="' . $name . '"/>';
    }
    $paymentsList .= '<script type="text/javascript">function paymark_verify(merchant) { window.open ("http://www.paymark.co.nz/dart/darthttp.dll?etsl&tn=verify&merchantid=" + merchant, "verify", "scrollbars=yes, width=400, height=400"); }</script>';
    $paymentsList.='<img height="30" src="payment_NZ_gateways/images/paymark.png" alt="Paymark Certified" onclick="paymark_verify (' . "'" . self::$spt_merchant_id . "'" . ')"/></div>';
    return new FieldSet (
      new TextField('CardHolderName', 'Card Holder Name:'),
      new TextField('CreditCardNumber', 'Credit Card Number:'),
      new NumericField('CreditCardExpiry', 'Credit Card Expiry (MMYY):', '', 4),
      new LiteralField('SPTInfo', $paymentsList)
    );
  }

  /**
   * Returns the required fields to add to the order form, when using this
   * payment method.
   */
  function getPaymentFormRequirements() {
      return array (
        "js" => "
          require('CardHolderName');
          require('CreditCardNumber');
          require('CreditCardExpiry');
        ",
      "php" => '
        $this->requireField("CardHolderName", $data);
        $this->requireField("CreditCardNumber", $data);
        $this->requireField("CreditCardExpiry", $data);
      ',
    );
  }

  /**
   * Process payment using HTTPS POST
   */
  function processPayment($data, $form) {
    $orderRef = $this->ID;
    $cardNo = $data['CreditCardNumber'];
    $cardExp = $data['CreditCardExpiry'];
    $cardHolder = $data['CardHolderName'];
    $cardType = 0;
    $amt = $this->Amount;
    $currency = $this->Currency;

    $postvars = array(
      'OrderReference' => $orderRef,
      'CardNumber' => $cardNo,
      'CardExpiry' => $cardExp,
      'CardHolderName' => $cardHolder,
      'CardType' => $cardType,
      'MerchantID' => self::$spt_merchant_id,
      'MerchantKey' => self::$spt_merchant_key,
      'Amount' => $amt,
      'Currency' => $currency
    );

    $response = $this->http_post('https','tx.securepaytech.com',8443,'/web/HttpPostPurchase', $postvars);
    $responses = explode (',', $response);
    //var_dump ($responses);
    $ok = $responses[0] == 1;
    if ($ok) {
      $this->Status = 'Success';
      $result = new Payment_Success();
    }
    else {
      $this->Status = 'Failure';
      $error_reasons = array ('', '', 'Insufficient funds', 'Card expired', 'Card declined', 'Server error occurred', 'Communication error', 'Unsupported transaction type', 'Bad or malformed request', 'Invalid card number');
      $this->Message = $error_reasons [$responses [0]];
      $result = new Payment_Failure();
    }
    $this->write();
    return $result;
  }

  /* $vars is an associative array containing the post variables */
  function http_post($method,$server, $port, $url, $vars) {
    $postdata = "";
    foreach($vars as $key => $value) {
      $postdata .= urlencode($key) . "=" . urlencode($value) . "&";
    }
    $postdata = substr($postdata,0,-1);
    $content_length = strlen($postdata);
    $headers = "POST $url HTTP/1.1\r\n".
      "Accept: */*\r\n".
      "Accept-Language: en-nz\r\n".
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "Host: $server\r\n".
      "Connection: Keep-Alive\r\n".
      "Cache-Control: no-cache\r\n".
      "Content-Length: $content_length\r\n\r\n";
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $method . '://' . $server .":". $port . $url);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
    $ret = curl_exec($ch);
     curl_close($ch);
     return $ret;
  }

}
