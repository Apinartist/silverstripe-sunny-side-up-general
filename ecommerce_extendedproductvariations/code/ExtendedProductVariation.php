<?php
/**
 * @author nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 * Extends product variation - links to many options
   */
class ExtendedProductVariation extends ProductVariation {

	static $belongs_many_many = array(
		'ExtendedProductVariationOptions' => 'ExtendedProductVariationOption'
	);

	protected static $EPVOptionIDArray = array();

	static function get_live_stage() {
		return "";
	}

	function getCMSFields_forPopup() {
		$fields = array();
		$fields[] = new TextField('Title');
		$fields[] = new TextField('Price');
		return new FieldSet($fields);
	}

	static function return_existing_or_create_new($optionsDos, $productID) {
		//look for it ...
		$optionsDosArray = array();
		if(!isset(self::$EPVOptionIDArray["optionsFromEPV"])) {
			self::$EPVOptionIDArray["optionsFromEPV"] = array();
		}
		foreach($optionsDos as $option) {
			$optionsDosArray[$option->ID] = $option->ID;
		}
		if(!isset(self::$EPVOptionIDArray[$productID])) {
			self::$EPVOptionIDArray[$productID] = DataObject::get("ExtendedProductVariation", '`ProductID` = '.$productID);
		}
		if(self::$EPVOptionIDArray[$productID]) {
			foreach(self::$EPVOptionIDArray[$productID] as $EPV) {
				$theOne = true;
				if(!isset(self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID])) {
					self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID] = array();
					foreach($EPV->ExtendedProductVariationOptions() as $option) {
						self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID][$option->ID] = $option->ID;
					}
				}
				if(count(self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID]) == count($optionsDosArray)) {
					foreach(self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID] as $key => $value) {
						if(!isset($optionsDosArray[$key])) {
							$theOne = false;
						}
						elseif($optionsDosArray[$key] != $value) {
							$theOne = false;
						}
						else {
							foreach($optionsDosArray as $optionsDosArrayKey => $optionsDosArrayValue) {
								if(!isset(self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID][$optionsDosArrayKey])) {
									$theOne = false;
								}
								elseif(self::$EPVOptionIDArray["optionsFromEPV"][$EPV->ID][$optionsDosArrayKey] != $optionsDosArrayValue) {
									$theOne = false;
								}
							}
						}
					}
				}
				else {
					$theOne = false;
				}
				if($theOne && $EPV) {
					//this is the one we are looking for....
					return $EPV;
				}
			}
		}
		return new ExtendedProductVariation();
	}

	function getPrice() {
	//DIRTY HACK!!!! NOT SURE WHY WE NEED THIS
		$object = DataObject::get_by_id("ProductVariation", $this->ID);
		if($object) {
			$array = $object->record;
			return $array["Price"];
		}
		else {
			return 0;
		}
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}

	function onBeforeDelete() {
		parent::onBeforeDelete();
	}

	function getProductID() {
		return $this->ID;
	}

}
