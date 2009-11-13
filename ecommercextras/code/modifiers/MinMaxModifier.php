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
		static function set_default_min_quantity($v) { self::$default_min_quantity = $v;}

	protected static $default_max_quantity = 99;
		static function set_default_max_quantity($v) { self::$default_max_quantity = $v;}

	protected static $min_field = "MinQuantity";
		static function set_min_field($v) { self::$min_field = $v;}

	protected static $max_field = "MaxQuantity";
		static function set_max_field($v) { self::$max_field = $v;}

	protected static $adjustment_message = "Quantities have been adjusted as follows: ";
		static function set_adjustment_message($v) { self::$adjustment_message = $v;}

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
						$newQuantity = 0;
						$absoluteMin = 0;
						$absoluteMax = 99999;
						if($minFieldName) {
							if($product->$minFieldName) {
								$absoluteMin = $product->$minFieldName;
								if($quantity < $product->$minFieldName) {
									$newQuantity = $product->$minFieldName;
								}
							}
						}
						elseif(self::$default_min_quantity) {
							if($absoluteMin < self::$default_min_quantity ) {
								$absoluteMin = self::$default_min_quantity;
							}
							if($quantity < self::$default_min_quantity) {
								$newQuantity = self::$default_min_quantity;
							}
						}
						if($maxFieldName) {
							if($product->$maxFieldName) {
								$absoluteMax = $product->$maxFieldName;
								if($quantity < $product->$maxFieldName) {
									$newQuantity = $product->$maxFieldName;
								}
							}
						}
						elseif(self::$default_max_quantity) {
							if($absoluteMax > self::$default_max_quantity) {
								$absoluteMax = self::$defaul_max_quantity;
							}

							if($quantity < self::$default_max_quantity) {
								$newQuantity = self::$default_max_quantity;
							}
						}
						if($newQuantity != $quantity && $newQuantity) {
							ShoppingCart::set_quantity_item($product->ID, $newQuantity);
							$msgArray[$i] = $product->Title.": ".$newQuantity;
							$i++;
						}
						if($absoluteMin || $absoluteMax < 99999) {
							$js = '
								jQuery("#Product_OrderItem_'.$product->ID.'_Quantity").blur(
									function() {
										if(jQuery(this).val() > '.intval($absoluteMax).') {
											jQuery(this).val('.intval($absoluteMax).');
										}
										if(jQuery(this).val() < '.intval($absoluteMin).') {
											jQuery(this).val('.intval($absoluteMin).');
										}
									}
								);';
							Requirements::customScript($js,'Product_OrderItem_'.$product->ID.'_Quantity');
						}
					}
				}
			}
		}
		if(self::$adjustment_message && count($msgArray)) {
			$msg = self::$adjustment_message.'\n'.implode('\n',$msgArray);
			if($msg) {
				Requirements::customScript('alert("'.Convert::raw2js($msg).'");');
			}
		}
	}

	function updateForAjax(array &$js) {
		self::apply_min_max();
	}


//--------------------------------------------------------------------*** database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}


}

