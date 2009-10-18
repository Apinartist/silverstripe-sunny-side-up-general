<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 **/

class PickUpOrDeliveryModifierOptions extends DataObject {

	static $db = array(
		"IsDefault" => "Boolean",
		"Code" => "Varchar(25)",
		"Name" => "Varchar(175)",
		"MinimumDeliveryCharge" => "Currency",
		"MaximumDeliveryCharge" => "Currency",
		"MinimumOrderAmountForZeroRate" => "Currency",
		"WeightMultiplier" => "Double",
		"Percentage" => "Double",
		"FixedCost" => "Currency",
	);

	public static $many_many = array(
		"AvailableInCountries" => "PickUpOrDeliveryModifierOptionsCountry",
	);

	public static $indexes = array(
		"Code" => true
	);

	public static $searchable_fields = array(
		"Code",
		"Name" => "PartialMatchFilter"
	);

	public static $field_labels = array(
		"IsDefault" => "Default",
		"Code" => "Code",
		"Name" => "Long Name",
		"MinimumDeliveryCharge" => "Minimum",
		"MaximumDeliveryCharge" => "Maximum",
		"MinimumOrderAmountForZeroRate" => "Min. for 0 rate",
		"WeightMultiplier" => "WeightMultiplier per kg.",
		"Percentage" => "Percentage",
		"FixedCost" =>  "Fixed Cost",
	);

	public static $defaults = array(
		"Code" => "deliverycode",
		"Code" => "Home Delivery",
		"MinimumDeliveryCharge" => 10,
		"MaximumDeliveryCharge" => 9999999,
		"MinimumOrderAmountForZeroRate" => 9999999,
		"WeightMultiplier" => 0,
		"Percentage" => 0,
		"FixedCost" => 10
	);

	public static $summary_fields = array(
		"IsDefault",
		"Code",
		"Name"
	);

	public static $singular_name = "Delivery / Pick-up Option";

	public static $plural_name = "Delivery / Pick-up Options";

	static function default_object() {
		if($obj = DataObject::get_one("PickUpOrDeliveryModifierOptions", $filter = "`IsDefault` = 1")) {
			return $obj;
		}
		else {
			$obj = new PickUpOrDeliveryModifierOptions();
			$obj->IsDefault = 1;
			$obj->write();
			return $obj;
		}
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("AvailableInCountries", $this->createManyManyComplexTableField());
		return $fields;
	}

	private function createManyManyComplexTableField() {
		$field = new ManyManyComplexTableField(
			$this,
			'AvailableInCountries',
			'PickUpOrDeliveryModifierOptionsCountry',
			array(
				'Name' => 'Name',
			)
		);
		$field->setAddTitle("Select Countries for which this delivery / pick-up option is available");
		$field->setPageSize(250);
		return $field;
	}


	function onBeforeWrite() {
		// no other record but current one is not default
		if(!$this->IsDefault && !DataObject::get_one("PickUpOrDeliveryModifierOptions", "`ID` <> ".intval($this->ID))) {
			$this->IsDefault = 1;
		}
		//current default -> reset others
		elseif($this->IsDefault) {
			DB::query('UPDATE `PickUpOrDeliveryModifierOptions` SET `IsDefault` = 0 WHERE `ID` <> '.intval($this->ID).';');
		}
		$i = 0;
		if(!$this->Code) {
			$this->Code = self::$defaults["Code"];
		}
		$baseCode = $this->Code;
		while($other = DataObject::get_one("PickUpOrDeliveryModifierOptions", '`Code` = "'.$this->Code.'" AND `ID` <> '.$this->ID)){
			$this->Code = $baseCode.'_'.$i;
		}
		parent::onBeforeWrite();
	}
}

