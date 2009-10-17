<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 **/

class PickUpOrDeliveryModifierDataCountry extends DataObject {

	static $db = array(
		"Code" => "Varchar(3)",
		"Name" => "Varchar(200)",
	);

	static $indexes = array(
		"Code" => true
	);

	static $default_sort = "Name";

	static $belongs_many_many = array(
		"CountryMustBeLink" => "PickUpOrDeliveryModifierData",
		"CountryCanNotBeLink" => "PickUpOrDeliveryModifierData"
	);

	public static $singular_name = "Country";

	public static $plural_name = "Countries";


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->push(new TextField("test"));
		return $fields;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!DataObject::get("PickUpOrDeliveryModifierDataCountry")) {
			$array = Geoip::getCountryDropDown();
			foreach($array as $key => $value) {
				$obj = new PickUpOrDeliveryModifierDataCountry();
				$obj->Code = $key;
				$obj->Name = $value;
				$obj->write();
			}
		}
	}
}

