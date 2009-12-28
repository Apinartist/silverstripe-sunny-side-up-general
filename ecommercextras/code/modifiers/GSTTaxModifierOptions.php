<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 **/

class GSTTaxModifierOptions extends DataObject {

	static $db = array(
		"CountryCode" => "Varchar(3)",
		"Code" => "Varchar(12)",
		"Name" => "Varchar(175)",
		"InclusiveOrExclusive" => "Enum('Inclusive,Exclusive', 'Exclusive')",
		"Rate" => "Double",
		"PriceSuffix" => "Varchar(25)"
	);

	public static $defaults = array(
		"CountryCode" => "NZ"
	);
	public static $indexes = array(
		"Code" => true
	);

	public static $searchable_fields = array(
		"Code",
		"Name" => "PartialMatchFilter",
	);

	public static $field_labels = array(
		"CountryCode" => "Country Code",
		"Code" => "Code for tax",
		"Name" => "Name for tax",
		"InclusiveOrExclusive" => "Inclusive/Exclusive",
		"Rate" => "Rate (e.g. 0.125)",
		"PriceSuffix" => "Price Suffix"
	);

	public static $summary_fields = array(
		"CountryCode",
		"Code",
		"Name",
		"InclusiveOrExclusive",
		"Rate"
	);

	public static $singular_name = "Tax";

	public static $plural_name = "Taxes";


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("CountryCode", new DropDownField("CountryCode", self::$field_labels["CountryCode"], Geoip::getCountryDropDown()));
		return $fields;
	}

	function onBeforeWrite() {
		if($this->ID && DataObject::get_one("GSTTaxModifierOptions", '`CountryCode` = "'.$this->CountryCode.'" AND `ID` <> '.$this->ID)) {
			die("can not save two taxes for one country!");
		}
		parent::onBeforeWrite();
	}


}

