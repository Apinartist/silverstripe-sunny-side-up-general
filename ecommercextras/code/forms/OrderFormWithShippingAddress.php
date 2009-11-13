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
			"ShippingDetails",
			new HeaderField('Delivery Address', 3),
			new LiteralField('ShippingNote', '<p class="warningMessage"><em>Your goods will be sent to the address below.</em></p>'),
			new TextField('ShippingName', 'Name', null, $this),
			new TextField('ShippingAddress', 'Address', null, $this),
			new TextField('ShippingAddress2', '', null, $this),
			new TextField('ShippingCity', 'City', null, $this),
			$countryField,
			new HiddenField('UseShippingAddress', '')
		);
		//$this->fields->push($shippingFields);
		$this->fields->addFieldToTab("",$shippingFields);
	}

	function processOrder($data, $form, $request) {
		return parent::processOrder($data, $form, $request);
	}

}

