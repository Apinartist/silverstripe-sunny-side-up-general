<?php
 /**
  * Order form that allows a user to purchase their
  * order items on the
  *
  * @see CheckoutPage
  *
  * @package ecommerce
  */
class OrderFormWithoutShippingAddress extends OrderForm {

	protected static $fixed_country_code;

	static function set_fixed_country_code($v) {
		self::$fixed_country_code = $v;
	}


	function __construct($controller, $name) {
		$myForm = parent::__construct($controller, $name);
		$this->unsetActionByName("useDifferentShippingAddress");
		$this->fields->addFieldToTab("", new CheckboxField("ENews", "Sign-up to e-news"));
		$this->fields->addFieldToTab("", new CheckboxField("ProNewsletter", "Sign-up to pro-newsletter"));
		if($message = $this->CustomErrors()) {
			$this->fields->addFieldToTab("", new HeaderField("ErrorHeading", "Error!"));
			$this->fields->addFieldToTab("", new LiteralField("ErrorMessage", '<div class="error">'.$message.'</div>'));
		}
		$this->resetField("Country", "NZ");
		Requirements::customscript('
			jQuery("#OrderFormWithoutShippingAddress_OrderForm_action_useDifferentShippingAddress").hide();
		');
		if(self::$fixed_country_code) {
			$this->resetField("Country", self::$fixed_country_code);
		}
		$orderForm = new OrderForm($this, "OrderForm");
		$data = $orderForm->getData();
		$this->loadDataFrom($data);
	}


	function CustomErrors() {
		$v = Session::get("FormInfo.OrderFormWithoutShippingAddress.formError.message").Session::get("FormInfo.OrderForm_OrderForm.formError.message");
		return $v;
	}

	function setupFormErrors() {
		//parent::setupFormErrors();
		$errorInfo = Session::get("FormInfo.OrderForm_OrderForm");
		if(isset($errorInfo['errors']) && is_array($errorInfo['errors'])) {
			foreach($errorInfo['errors'] as $error) {
				$field = $this->fields->dataFieldByName($error['fieldName']);

				if(!$field) {
					$errorInfo['message'] = $error['message'];
					$errorInfo['type'] = $error['messageType'];
				} else {
					$field->setError($error['message'], $error['messageType']);
				}
			}

			// load data in from previous submission upon error
			if(isset($errorInfo['data'])) $this->loadDataFrom($errorInfo['data']);
		}

		if(isset($errorInfo['message']) && isset($errorInfo['type'])) {
			$this->setMessage($errorInfo['message'], $errorInfo['type']);
		}


	}

	/**
	 * Disable the validator when the action clicked is to use a different shipping address
	 * or use the member shipping address.
	 */

	function processOrder($data, $form, $request) {
		$paymentClass = (!empty($data['PaymentMethod'])) ? $data['PaymentMethod'] : null;
		$payment = class_exists($paymentClass) ? new $paymentClass() : null;

		if(!($payment && $payment instanceof Payment)) {
			user_error(get_class($payment) . ' is not a valid Payment object!', E_USER_ERROR);
		}

		if(!ShoppingCart::has_items()) {
			$form->sessionMessage('Please add some items to your cart', 'bad');
	   	Director::redirectBack();
	   	return false;
		}

		// Create new OR update logged in {@link Member} record
		$member = EcommerceRole::createOrMerge($data);
		if(!$member) {
			$form->sessionMessage(
				_t(
					'OrderForm.MEMBEREXISTS', 'Sorry, a member already exists with that email address.
					If this is your email address, please log in first before placing your order.'
				),
				'bad'
			);

			//Director::redirectBack();
			//return false;
		}
		$member->write();
		$member->logIn();

		// Create new Order from shopping cart, discard cart contents in session
		$order = ShoppingCart::save_current_order();
		ShoppingCart::clear();

		// Write new record {@link Order} to database
		$form->saveInto($order);
		$order->write();

		// Save payment data from form and process payment
		$form->saveInto($payment);
		$payment->OrderID = $order->ID;
		$payment->Amount = $order->Total();
		$payment->write();

		// Process payment, get the result back
		$result = $payment->processPayment($data, $form);

		// isProcessing(): Long payment process redirected to another website (PayPal, Worldpay)
		if($result->isProcessing()) {
			return $result->getValue();
		}

		if($result->isSuccess()) {
			$order->sendReceipt();
		}
		Director::redirect($order->Link());
		return true;
	}





}

