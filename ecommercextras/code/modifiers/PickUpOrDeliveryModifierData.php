<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 **/

class PickUpOrDeliveryModifierData extends DataObject {

	static $db = array(
		"IsDefault" => "Boolean",
		"Code" => "Varchar(25)",
		"Name" => "Varchar(25)",
		"MinimumDeliveryCharge" => "Currency",
		"MaximumDeliveryCharge" => "Currency",
		"MinimumOrderAmountForZeroRate" => "Currency",
		"WeightMultiplier" => "Double",
		"Percentage" => "Double",
		"FixedCost" => "Currency"
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
		"FixedCost" =>  "Fixed Cost"
	);

	public static $defaults = array(
		"Code" => "deliverycode",
		"MinimumDeliveryCharge" => 0,
		"MaximumDeliveryCharge" => 99999,
		"MinimumOrderAmountForZeroRate" => 99999,
		"WeightMultiplier" => 0,
		"Percentage" => 0,
		"FixedCost" => 0
	);

	public static $summary_fields = array(
		"IsDefault",
		"Code",
		"Name"
	);

	public static $singular_name = "Delivery / Pick-up Option";

	public static $plural_name = "Delivery / Pick-up Options";

	function onBeforeWrite() {
		// no other record but current one is not default
		if(!$this->IsDefault && !DataObject::get_one("PickUpOrDeliveryModifierData", "`ID` <> ".intval($this->ID))) {
			$this->IsDefault = 1;
		}
		//current default -> reset others
		elseif($this->IsDefault) {
			DB::query('UPDATE `PickUpOrDeliveryModifierData` SET `IsDefault` = 0 WHERE `ID` <> '.intval($this->ID).';');
		}
		$i = 0;
		if(!$this->Code) {
			$this->Code = self::$defaults["Code"];
		}
		$baseCode = $this->Code;
		while($other = DataObject::get_one("PickUpOrDeliveryModifierData", '`Code` = "'.$this->Code.'" AND `ID` <> '.$this->ID)){
			$this->Code = $baseCode.'_'.$i;
		}
		parent::onBeforeWrite();
	}
}

