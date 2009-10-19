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
		"IsDefault" => true,
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
		"MinimumOrderAmountForZeroRate" => "Minimum for 0 rate (i.e. if the total order is over ... then there is no fee for this option)",
		"WeightMultiplier" => "WeightMultiplier per kg. (works out weight of order (make sure products have weight) and multiplies with this number to work out charge for this option)",
		"Percentage" => "Percentage of total order cost as charge for this option",
		"FixedCost" =>  "This option has a fixed cost (e.g. always $10)"
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

	static function get_all_as_country_array() {
		$array = array();
		$Options = DataObject::get("PickUpOrDeliveryModifierOptions");
		foreach($Options as $option) {
			if($countries = $option->AvailableInCountries()) {
				foreach($countries as $country) {
					$array[$option->Code][] = $country->Code;
				}
			}
		}
		return $array;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("AvailableInCountries", $this->createManyManyComplexTableField());
		return $fields;
	}

	private function createManyManyComplexTableField() {
		$title = '';
		if(class_exists("MultiSelectField")) {
			$array = DataObject::get("PickUpOrDeliveryModifierOptionsCountry")->toDropdownMap('ID','Title');
			//$name, $title = "", $source = array(), $value = "", $form = null
			$field = new MultiSelectField(
				'AvailableInCountries',
				'This option is available in...',
				$array
			);
		}
		else {
			// $controller,  $name,  $sourceClass, [ $fieldList = null], [ $detailFormFields = null], [ $sourceFilter = ""], [ $sourceSort = ""], [ $sourceJoin = ""]
			$field = new ManyManyComplexTableField(
				$this,
				'AvailableInCountries',
				'PickUpOrDeliveryModifierOptionsCountry',
				array('Name' => 'Name'),
				null,
				null,
				"`Checked` DESC, `Name` ASC"
			);
			$field->setAddTitle("Select Countries for which this delivery / pick-up option is available");
			$field->setPageSize(250);
		}
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
		$this->Code = eregi_replace("[^[:alnum:]]", " ", $this->Code );
		$this->Code = trim(eregi_replace(" +", "", $this->Code));
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

