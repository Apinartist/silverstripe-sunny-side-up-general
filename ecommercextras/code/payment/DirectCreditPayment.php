<?php

/**
 * Payment object representing a DirectCredit payment.
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package payment
 */
class DirectCreditPayment extends Payment {

	/**
	 * Process the DirectCredit payment method
	 */
	function processPayment($data, $form) {
		$this->Status = 'Pending';
		$this->Message = '<p class="warningMessage">' . _t('DirectCreditPayment.MESSAGE', 'Payment accepted via Direct Credit. Please note : products will not be shipped until payment has been received.') . '</p>';
		$this->write();
		return new Payment_Success();
	}

	function getPaymentFormFields() {
		return new FieldSet(
			new HiddenField("DirectCredit", "DirectCredit", 0)
		);
	}

	function getPaymentFormRequirements() {
		return null;
	}


}

