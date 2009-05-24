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
		parent::__construct($controller, $name);
		$this->unsetActionByName("useDifferentShippingAddress");
		$this->resetField("Country", "NZ");
		Requirements::customscript('
			jQuery("#OrderFormWithoutShippingAddress_OrderForm_action_useDifferentShippingAddress").hide();
		');
		if(self::$fixed_country_code) {
			$this->resetField("Country", self::$fixed_country_code);
		}
	}

	/**
	 * Disable the validator when the action clicked is to use a different shipping address
	 * or use the member shipping address.
	 */
	function beforeProcessing() {
		parent::beforeProcessing();
	}



}
?>