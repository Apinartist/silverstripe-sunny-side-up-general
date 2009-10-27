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

	static function return_existing_or_create_new($title, $optionsDos, $productID) {
		if($obj = DataObject::get_one("ExtendedProductVariation", '`ProductID` = '.$productID.' AND `Title` = "'.$title.'"')) {
			return $obj;
		}
		//look for it ...
		$checkArray = array();
		foreach($optionsDos as $option) {
			$checkArray[$option->ID] = $option->ID;
		}
		$EPVs = DataObject::get("ExtendedProductVariation", '`ProductID` = '.$productID);
		if($EPVs) {
			foreach($EPVs as $EPV) {
				$canExist = true;
				$array = array();
				foreach($EPV->ExtendedProductVariationOptions() as $option) {
					$array[$option->ID] = $option->ID;
				}
				if(count($array) == count($checkArray)) {
					foreach($array as $key => $value) {
						if(!isset($checkArray[$key])) {
							$canExist = false;
						}
						elseif($checkArray[$key] != $value) {
							$canExist = false;
						}
					}
				}
				else {
					$canExist = false;
				}
				if($canExist == true) {
					return $EPV;
				}
			}
		}
		return new ExtendedProductVariation();
	}

	function getPrice() {
	//DIRTY HACK!!!! NOT SURE WHY WE NEED THIS
		$object = DataObject::get_by_id("ProductVariation", $this->ID);
		$array = $object->record;
		return $array["Price"];
	}

}
