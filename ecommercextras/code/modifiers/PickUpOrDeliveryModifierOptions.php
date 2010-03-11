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
		"Sort" => "Int",
		"AcceptablePaymentMethods" => "Varchar(255)",
	);

	public static $has_one = array(
		"ExplanationPage" => "SiteTree"
	);

	public static $many_many = array(
		"AvailableInCountries" => "PickUpOrDeliveryModifierOptionsCountry"
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
		"Percentage" => "Percentage (number between 0 = 0% and 1 = 100%) of total order cost as charge for this option (e.g. 0.05 would add 5 cents to every dollar ordered)",
		"FixedCost" =>  "This option has a fixed cost (e.g. always 10 dollars)",
		"Sort" =>  "Sort Index"
	);

	public static $defaults = array(
		"Code" => "homedelivery",
		"Name" => "Home Delivery",
		"MinimumDeliveryCharge" => 10,
		"MaximumDeliveryCharge" => 100,
		"MinimumOrderAmountForZeroRate" => 50,
		"WeightMultiplier" => 0,
		"Percentage" => 0,
		"FixedCost" => 10,
		"Sort" => 100
	);

	public static $summary_fields = array(
		"IsDefault",
		"Code",
		"Name"
	);

	public static $singular_name = "Delivery / Pick-up Option";

	public static $plural_name = "Delivery / Pick-up Options";

	public static $default_sort = "IsDefault DESC, Sort ASC, Name ASC";

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
		if($Options) {
			foreach($Options as $option) {
				if($countries = $option->AvailableInCountries()) {
					foreach($countries as $country) {
						$array[$option->Code][] = $country->Code;
					}
				}
			}
		}
		return $array;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$field = $this->createManyManyComplexTableField();
		if($field) {
			$fields->replaceField("AvailableInCountries", $field);
		}
		if(class_exists("DataObjectSorterController") && $this->hasExtension("DataObjectSorterController")) {
			$fields->addFieldToTab("Root.SortList", new LiteralField("InvitationToSort", $this->dataObjectSorterPopupLink()));
		}
		fields->replaceField("ExplanationPageID", new TreeDropdownField($name = "ExplanationPageID", $title = "Link to page explaining postage / delivery (if any)", "SiteTree" ));
		return $fields;
	}

	private function createManyManyComplexTableField() {
		$title = '';
		$field = null;
		if(class_exists("MultiSelectField")) {
			$dos = DataObject::get("PickUpOrDeliveryModifierOptionsCountry");
			if($dos) {
				$array = $dos->toDropdownMap('ID','Title');
				//$name, $title = "", $source = array(), $value = "", $form = null
				$field = new MultiSelectField(
					'AvailableInCountries',
					'This option is available in...',
					$array
				);
			}
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
			$field->setPermissions(array("export"));
			$field->setPageSize(250);
		}
		if($field) {
			return $field;
		}
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

