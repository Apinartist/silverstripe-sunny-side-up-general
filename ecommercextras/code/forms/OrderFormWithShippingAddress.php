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
		$countryField = new DropdownField('ShippingCountry', 'Country', Geoip::getCountryDropDown(), $defaulCountry, $this);

		$shippingFields = new Tab(
			"ShippingDetails",
			new HeaderField('Delivery Address', 3, $this),
			new LiteralField('ShippingNote', '<p class="warningMessage"><em>Your goods will be sent to the address below.</em></p>'),
			new TextField('ShippingName', 'Name', null, 100, $this),
			new TextField('ShippingAddress', 'Address', null,100,  $this),
			new TextField('ShippingAddress2', '', null, 100, $this),
			new TextField('ShippingCity', 'City', null, 100, $this),
			$countryField,
			new HiddenField('UseShippingAddress', '', true, $this)
		);
		//$this->fields->push($shippingFields);
		$this->fields->addFieldToTab("",$shippingFields);
		foreach($this->fields->dataFields() as $i => $child) {
			if(is_object($child)){
				$name = $child->Name();
				switch ($name) {
					case "Address":
						$child->setTitle('Address for invoicing');
						break;
					default:
						break;
				}
			}
		}
	}

	function processOrder($data, $form, $request) {
		return parent::processOrder($data, $form, $request);
	}

}

