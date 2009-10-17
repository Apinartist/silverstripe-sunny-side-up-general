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
		"Code" => "True"
	);

	static $belongs_many_many = array(
		"PickUpOrDeliveryModifierData" => "PickUpOrDeliveryModifierData"
	);

	public static $singular_name = "Country";

	public static $plural_name = "Countries";

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!DataObject::get("PickUpOrDeliveryModifierDataCountry")) {
			$array = Geoip::getgetCountryDropDown();
			foreach($array as $key => $value) {
				$obj = new PickUpOrDeliveryModifierDataCountry();
				$obj->Code = $key;
				$obj->Name = $value;
			}
		}
	}
}

