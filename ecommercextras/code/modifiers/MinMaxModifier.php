<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description: makes sure that a product quantity in cart stays between a min and a max
 */
class MinMaxModifier extends OrderModifier {

//--------------------------------------------------------------------*** static variables

	static $db = array();

	protected static $title = "MinMaxModifier";

	protected static $default_min_quantity = 1;
		static functon set_default_min_quantity($v) { self::$default_min_quantity = $v;}

	protected static $default_max_quantity = 99;
		static functon set_default_max_quantity($v) { self::$default_max_quantity = $v;}

	protected static $min_field = "MinQuantity";
		static functon set_min_field($v) { self::$min_field = $v;}

	protected static $max_field = "MaxQuantity";
		static functon set_max_field($v) { self::$max_field = $v;}

	protected static $adjustment_message = "Quantities have been adjusted as follows: ";
		static functon set_adjustment_message($v) { self::$adjustment_message = $v;}

//-------------------------------------------------------------------- *** static functions

	static function show_form() {
		self::apply_min_max();
		return false;
	}

	static function get_form($controller) {
		return false;
	}

//-------------------------------------------------------------------- *** display functions
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return false;
	}


//--------------------------------------------------------------------*** table values
	function LiveAmount() {
		return 0;
	}

	function TableValue() {
		return "";
	}


//--------------------------------------------------------------------*** table titles
	function LiveName() {
		return self::$title;
	}

	function Name() {
		return self::$title;
	}

	function TableTitle() {
		return $this->Name();
	}


//-------------------------------------------------------------------- *** calculations
	static function apply_min_max() {
		if(self::$min_field || self::$max_field  || self::$default_min_quantity || self::$default_max_quantity ) {
			$msgArray = array();
			$minFieldName = self::$min_field;
			$maxFieldName = self::$max_field;
			$items = ShoppingCart::get_items();
			$i = 0;
			foreach($items as $itemIndex => $item) {

				if($item) {
					$product = $item->Product();

					if($quantity = $item->getQuantity()) {
						//
						if($minFieldName) {
							if($quantity < $product->$minFieldName) {
								$newQuantity = $product->$minFieldName;
							}
						}
						elseif(self::$default_min_quantity) {
							if($quantity < self::$default_min_quantity) {
								$newQuantity = self::$default_min_quantity;
							}
						}
						if($maxFieldName) {
							if($quantity < $product->$maxFieldName) {
								$newQuantity = $product->$maxFieldName;
							}
						}
						elseif(self::$default_max_quantity) {
							if($quantity < self::$default_max_quantity) {
								$newQuantity = self::$default_max_quantity;
							}
						}
						$product = DataObject::get_by_id("SiteTree", $bonusItemID);
						if($newQuantity != $quantity) {
							ShoppingCart::set_quantity_item($product->ID, $newQuantity);
							$msgArray[$i] = $product->Title.": ".$newQuantity;
							$i++;
						}
					}
				}
			}
		}
		if(self::$adjustment_message) {
			$msg = self::$adjustment_message.'\r\n'.implode("\r\n",$msgArray);
			if($msg) {
				Requirements::customScript('Alert("'.Convert::raw2js($msg).'");');
			}
		}
	}


//--------------------------------------------------------------------*** database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}


}

