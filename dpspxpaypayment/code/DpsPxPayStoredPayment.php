<?php

/**
 *@author nicolaas [at] sunny side up. co . nz
 *
 *
 **/


class DpsPxPayStoredPayment extends DpsPxPayPayment {


	function getPaymentFormFields() {
		$logo = '<img src="' . self::$logo . '" alt="Credit card payments powered by DPS"/>';
		$privacyLink = '<a href="' . self::$privacy_link . '" target="_blank" title="Read DPS\'s privacy policy">' . $logo . '</a><br/>';
		$paymentsList = '';
		foreach(self::$credit_cards as $name => $image) {
			$paymentsList .= '<img src="' . $image . '" alt="' . $name . '"/>';
		}

		$fields = new FieldSet();

		if($m = Member::currentMember()) {
			$storedCards = DataObject::get('DpsPxPayStoredCard', 'MemberID = '.$m->ID);
		}
		else {
			$storedCards = null;
		}

		$cardsDropdown = array('' => '');

		if($storedCards) {
			foreach($storedCards as $card) {
				$cardsDropdown[$card->BillingID] = $card->CardHolder.' - '.$card->CardNumber.' ('.$card->CardName.')';
			}

			$fields->push(new DropdownField('DPSUseStoredCard', 'Use a stored card?', $cardsDropdown, $value = $card->BillingID, $form = null, $emptyString = "--- use new Credit Card ---"));
		}
		else {
			$fields->push(new DropdownField('DPSStoreCard', '', array(1 => 'Store Credit Card', 0 => 'Do NOT Store Credit Card')));
		}
		$fields->push(new LiteralField('DPSInfo', $privacyLink));
		$fields->push(new LiteralField('DPSPaymentsList', $paymentsList));
		Requirements::javascript("dpspxpaypayment/javascript/DpxPxPayStoredPayment.js");
		return $fields;
	}

	function autoProcessPayment($amount, $ref) {
		$DPSUrl = $this->buildURL($amount, $ref, false);
		/*
		add CURL HERE
		$data = array('page' => $page);
		// create our curl object
		$ch = curl_init();
		$lurl = 'http://www.test.com';
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_URL, $lurl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
		*/

	}

	function processPayment($data, $form) {
		if(!isset($data["DPSUseStoredCard"])) {$data["DPSUseStoredCard"] = null;}
		if(!isset($data["DPSStoreCard"])) {$data["DPSStoreCard"] = null;}
		if(!isset($data["Amount"])) {USER_ERROR("There was no amount information for processing the payment.", E_USER_WARNING);}
		$url = $this->buildURL($data["Amount"], $data["DPSUseStoredCard"], $data["DPSStoreCard"]);
		return $this->executeURL($url);
	}

	protected function buildURL($amount, $cardToUse = '', $storeCard = false) {
		$commsObject = new DpsPxPayComs();

		/**
		* order details
		**/
		$commsObject->setTxnType('Purchase');
		$commsObject->setMerchantReference($this->ID);
		//replace any character that is NOT [0-9] or dot (.)
		$commsObject->setAmountInput(floatval(preg_replace("/[^0-9\.]/", "", $amount)));
		$commsObject->setCurrencyInput($this->Currency);

		if(isset($cardToUse)) {
			$commsObject->setBillingId($cardToUse);
		}
		else if($storeCard) {
			$commsObject->setEnableAddBillCard(1);
		}

		/**
		* details of the redirection
		**/
		$commsObject->setUrlFail(DpsPxPayStoredPayment_Handler::absolute_complete_link());
		$commsObject->setUrlSuccess(DpsPxPayStoredPayment_Handler::absolute_complete_link());

		/**
		* process payment data (check if it is OK and go forward if it is...
		**/
		$url = $commsObject->startPaymentProcess();
		return $url;

	}




}

class DpsPxPayStoredPayment_Handler extends DpsPxPayPayment_Handler {

	static $url_segment = 'dpspxpaystoredpayment';

	static function complete_link() {
		return self::$url_segment . '/paid/';
	}

	function paid() {
		$commsObject = new DpsPxPayComs();
		$response = $commsObject->processRequestAndReturnResultsAsObject();

		if($payment = DataObject::get_by_id('DpsPxPayStoredPayment', $response->getMerchantReference())) {
			if(1 == $response->getSuccess()) {
				$payment->Status = 'Success';

				if($response->DpsBillingId) {
					$existingCard = DataObject::get_one('DpsPxPayStoredCard', 'BillingID = '.$response->DpsBillingId);

					if($existingCard == false) {
						$storedCard = new DpsPxPayStoredCard();
						$storedCard->BillingID = $response->DpsBillingId;
						$storedCard->CardName = $response->CardName;
						$storedCard->CardHolder = $response->CardHolderName;
						$storedCard->CardNumber = $response->CardNumber;
						$storedCard->MemberID = $payment->Order()->MemberID;
						$storedCard->write();
					}
				}
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
