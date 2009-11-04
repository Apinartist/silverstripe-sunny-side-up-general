<?php
 /**
	* @author Nicolaas [at] sunnysideup.co.nz
  *
  * @see CheckoutPage
  *
  * @package ecommercextras
  */
class OrderFormWithoutShippingAddress extends OrderForm {

	protected static $fixed_country_code;

	protected static $extra_fields = array();

	protected static $postal_code_url = "http://www.nzpost.co.nz/Cultures/en-NZ/OnlineTools/PostCodeFinder";
	protected static $postal_code_label = "find postcode";

	static function set_fixed_country_code($v) {
		self::$fixed_country_code = $v;
	}

	static function add_extra_field($tabName, $field) {
		self::$extra_fields[] = array("TabName" =>$tabName, "FieldObject" => $field);
	}

	static function set_postal_code_url($v) {
		self::$postal_code_url = $v;
	}

	static function set_postal_code_label($v) {
		self::$postal_code_label = $v;
	}

	function __construct($controller, $name) {

		Requirements::javascript('ecommercextras/javascript/OrderFormWithoutShippingAddress.js');

		$myForm = parent::__construct($controller, $name);

		//stop people adding different shipping address
		$this->unsetActionByName("useDifferentShippingAddress");
		$member = Member::currentMember();
		if(!$member || !$member->ID || $member->Password == '') {
			$this->fields->addFieldToTab("", new LiteralField('MemberInfoAlso', '<p class="message good LoginCallToAction">Please <a href="Security/login?BackURL=' . CheckoutPage::find_link(true) . '/">log-in now</a> to retrieve your account details or create an account below.</p>'), "FirstName");
			//improve password field TEMPORARY HACK!
			//$passwordField = new OptionalConfirmedPasswordField('Password', 'Password', '', null, true);
			//$passwordField->minLength = 6;
			//$passwordField->showOnClickTitle = "add password now";
			//$this->fields->replaceField("Password", $passwordField);
			Requirements::javascript('ecommercextras/javascript/OptionalConfirmedPasswordField.js');
			Requirements::block(SAPPHIRE_DIR . '/javascript/ConfirmedPasswordField.js');
		}

		$this->fields->removeFieldFromTab("RightOrder", "Membership Details");
		$this->fields->removeFieldFromTab("RightOrder", "MemberInfo");
		//add extra fields
		foreach(self::$extra_fields as $fieldCombo) {
			$this->fields->addFieldToTab($fieldCombo["TabName"],$fieldCombo["FieldObject"]);
		}
		//replace field for address

		foreach($this->fields->dataFields() as $i => $child) {
			if(is_object($child)){
				$name = $child->Name();
				switch ($name) {
					case "Address":
						$child->setTitle('Delivery Address (no Postal Box)');
						break;
					case "AddressLine2":
						$child->setRightTitle('<a href="'.self::$postal_code_url.'" id="OrderFormWithoutShippingAddressPostalCodeLink">'.self::$postal_code_label.'</a>');
						$child->setTitle('Postal Code');
						break;
					default:
						break;
				}
			}
		}
		//$this->fields->replaceField("Address", new TextField("Address", "Delivery Address (no Postal Box)"));
		//$this->fields->replaceField("AddressLine2", $PostalCodeField);

		//$this->resetField("Country", "NZ");

		if(self::$fixed_country_code) {
			$this->resetField("Country", self::$fixed_country_code);
		}

		$this->fields->addFieldToTab("", new TextareaField('CustomerOrderNote', 'Note / Question'));

		//$orderForm = new OrderForm($this, "OrderForm"); ************ NOT SURE WHY WE NEED THIS!
		$data = $this->getData();
		$this->loadDataFrom($data);
	}



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
		$order->CustomerOrderNote = Convert::raw2sql($data["CustomerOrderNote"]);
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

