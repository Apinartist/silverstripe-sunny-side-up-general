<?php
 /**
	* @author Nicolaas [at] sunnysideup.co.nz
  *
  * @see CheckoutPage
  *
  * @package ecommercextras
  */
class OrderFormWithShippingAddress extends OrderFormWithoutShippingAddress {

	function __construct($controller, $name) {

		Requirements::javascript('ecommercextras/javascript/OrderFormWithShippingAddress.js');

		parent::__construct($controller, $name);
		if(self::$fixed_country_code) {
			$defaulCountry = self::$fixed_country_code;
		}
		else {
			$defaulCountry = EcommerceRole::findCountry();
		}
		$countryField = new DropdownField('ShippingCountry', 'Country', Geoip::getCountryDropDown(), $defaulCountry);

		$shippingFields = new Tab(
			new HeaderField('Delivery Address', 3),
			new LiteralField('ShippingNote', '<p class="warningMessage"><em>Your goods will be sent to the address below.</em></p>'),
			new TextField('ShippingName', 'Name'),
			new TextField('ShippingAddress', 'Address'),
			new TextField('ShippingAddress2', ''),
			new TextField('ShippingCity', 'City'),
			$countryField,
			new HiddenField('UseShippingAddress', '', true)
		);
		//$this->fields->push($shippingFields);
		$this->fields->addFieldToTab("",$shippingFields);
	}

	function processOrder($data, $form, $request) {
		return parent::processOrder($data, $form, $request);
	}

}

