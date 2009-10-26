<?php
/**
 * @author nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 * Extends product variation - links to many options
 */
class ExtendedProductVariation extends ProductVariation {

	static $versioning = array(
		'Stage'
	);

	static $extensions = array(
		"Versioned('Stage')"
	);


	static $belongs_many_many = array(
		'ExtendedProductVariationOptions' => 'ExtendedProductVariationOption'
	);

	static function get_live_stage() {
		return "";
	}

	function getCMSFields_forPopup() {
		$fields = array();
		$fields[] = new TextField('Title');
		$fields[] = new TextField('Price');
		return new FieldSet($fields);
	}

	static function does_not_exist_yet($title, $optionsDos, $productID) {
		if(DataObject::get_one("ExtendedProductVariation", '`ProductID` = '.$productID.' AND `Title` = "'.$title.'"')) {
			return false;
		}
		//look for it ...
		$checkArray = array();
		foreach($optionsDos as $option) {
			$checkArray[] = $option->ID;
		}
		$exists = false;
		$EPVs = DataObject::get("ExtendedProductVariation", '`ProductID` = '.$productID);
		if($EPVs) {
			foreach($EPVs as $EPV) {
				$array = array();
				foreach($EPV->ExtendedProductVariationOptions() as $option) {
					$array[] = $option->ID;
				}
				if($array == $checkArray) {
					$exists = true;
					break;
				}
			}
		}
		foreach($optionsDos as $key => $option) {
			if(0 == $key) {
				$innerJoin[] = "`ExtendedProductVariationOption_ExtendedProductVariations` AS T".$key;
			}
			else {
				$innerJoin[] = "INNER JOIN ExtendedProductVariationOption_ExtendedProductVariations AS T".$key." ON T0.`ExtendedProductVariationID` = T".$key.".`ExtendedProductVariationID`";
			}
			$innerJoin[] = "INNER JOIN ProductVariation AS Pv".$key." ON T".$key.".`ExtendedProductVariationID` = Pv".$key.".`ID`";
			$whereArray[] = "T".$key.".ExtendedProductVariationOptionID = ".$option->ID." AND Pv".$key.".`ProductID` = ".$productID;
		}
		$sql = '
			SELECT COUNT(T0.`ID`)
			FROM '.implode(" ",$innerJoin).'
			WHERE  '.implode(" AND ", $whereArray).';';
		$data = db::query($sql);
		$data->value() > 0 ? false:true;
	}

	function getPrice() {
	//DIRTY HACK!!!! NOT SURE WHY WE NEED THIS
		$object = DataObject::get_by_id("ProductVariation", $this->ID);
		$array = $object->record;
		return $array["Price"];
	}

}
