<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description: adds one or more bonus products
 * the product page / dataobject need to have a function BonusProductsForCart
 * which returns an array of IDs
 * each time a bonus item is added, it is added to Session::set("ecommercextras.bonusitems", $arrayofitems);
 * the following situations apply:
 * 1. item has not been added and thus will be added and qty set
 * 2. item has already been added and qty is updated
 * 3. item should be removed because parent item is no longer in cart
 * REQUIRES: ManageablePriceOrderItem from this module,
 * or another Product_OrderItem extension that has the function
 * "setAlternativeUnitPrice" for setting the bonus price.
 */
class BonusProductModifier extends OrderModifier {

// 					 *** static variables

	static $db = array(
		'Name' => 'Varchar(255)',
		'SerializedProductArray' => 'Text'
	);

	protected static $title = "Bonus Products";

	protected static $savings_calculated = false ;

	protected static $savings = 0;

	protected static $order_item_classname = "ManageablePriceOrderItem";

	protected static $bonus_product_array = null;

	protected static $bonus_price = 0;

// 					 *** static functions

	static function set_title($v) {
		self::$title = $v;
	}

	static function set_order_item_classname($v) {
		self::$order_item_classname = $v;
	}

	static function set_bonus_price($v) {
		self::$bonus_price = $v;
	}

	static function show_form() {
		return self::workout_savings();
	}

	static function get_form($controller) {
		return false;
	}

// 					 *** display functions
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return false;
	}

// 					 *** inclusive / exclusive functions
// 					 *** table values
	function LiveAmount() {
		return self::workout_savings();
	}

	function TableValue() {
		return "$".number_format(abs($this->Amount()), 2);
	}


// 					 *** table titles
	function LiveName() {
		return self::$title;
	}

	function Name() {
		if($this->ID) {
			return $this->Name;
		}
		else {
			return $this->LiveName();
		}
	}

	function TableTitle() {
		return $this->Name();
	}

	static function get_bonus_product_array() {
		if(!self::$bonus_product_array) {
			if(1 == 2) { //$this->ID ??? can not be used here, but should be used one way or another
				self::$bonus_product_array = unserialize($this->SerializedProductArray);
			}
			else {
				self::$bonus_product_array = Session::get("ecommercextras.bonusitems");
			}
		}
		return self::$bonus_product_array;
	}

// 					 *** calculations
	static function workout_savings() {
		if(!self::$savings_calculated) {
			self::$is_chargable = false;
			self::$savings = 0;
			$InCartIDArray = array();
			$newBonusProductArray = array();
			$oldBonusProductArray = array();

			//get ones already added
			$serializedOldBonusProductArray= self::get_bonus_product_array();
			if(!$serializedOldBonusProductArray) {
				$oldBonusProductArray = array();
				//debug::show("old bonus products to not exist yet");
			}
			else {
				$oldBonusProductArray = unserialize($serializedOldBonusProductArray);
				//debug::show("already added bonus products: ".print_r($oldBonusProductArray, true));
			}
			// work out bonus products based on items added to cart
			$items = ShoppingCart::get_items();
			foreach($items as $itemIndex => $item) {
				if($item) {
					$product = $item->Product();
					if($product && !array_key_exists($product->ID, $oldBonusProductArray)) {
						unset($tempIDArray);
						$tempIDArray = array();
						$tempIDArray = $product->BonusProductsForCart();
						foreach($tempIDArray as $bonusItemID) {
							if($quantity = $item->getQuantity()) {
								//debug::show("product ".$product->ID." has the following bonus products ".$bonusItemID." and QTY: $quantity" );
								//dont bother about adding it twice
								if(!array_key_exists($bonusItemID, $newBonusProductArray)) {
									$product = DataObject::get_by_id("SiteTree", $bonusItemID);
									//foreach product in cart get recommended products
									//debug::show("adding $product->Title as bonus product");
									$orderItem = new self::$order_item_classname($product);
									$orderItem->setAlternativeUnitPrice(self::$bonus_price);
									ShoppingCart::add_new_item($orderItem);
								}
								if(array_key_exists($bonusItemID, $newBonusProductArray)) {
									$quantity += $newBonusProductArray[$bonusItemID]["quantity"];
								}
								$newBonusProductArray[$bonusItemID] = array(
									"ID" => $bonusItemID,
									"quantity" => $quantity
								);
							}
						}
					}
				}
			}
			unset($items);
			$items = ShoppingCart::get_items();
			//get index numbers for bonus products - this can only be done now once they have actually been added
			foreach($items as $itemIndex => $item) {
				if($product = $item->Product()) {
					$bonusItemID = $product->ID;
					if(isset($newBonusProductArray[$bonusItemID]) && is_array($newBonusProductArray[$bonusItemID])) {
						$quantity = $newBonusProductArray[$product->ID]["quantity"];
						//debug::show("Updating qty for $bonusItemID to $quantity");
						ShoppingCart::set_quantity_item( $bonusItemID, $quantity);
						self::$savings += $newBonusProductArray[$bonusItemID]["quantity"] * $item->UnitPrice();
					}
				}
			}

			//remove old ones
			if($oldBonusProductArray && count($oldBonusProductArray) ) {
				//debug::show("there are old oldBonusProductArray");
				foreach($oldBonusProductArray as $array) {
					$bonusItemID = $array["ID"];
					//debug::show("wondering if we need to delete".$bonusItemID);
					if(!array_key_exists($array["ID"], $newBonusProductArray)) {
						//debug::show("removing bonus product: ".$bonusItemID);
						ShoppingCart::remove_all_item($bonusItemID);
					}
				}
			}
			$keys = array_keys($newBonusProductArray);
			if(is_array($newBonusProductArray) && count($newBonusProductArray)) {
				Requirements::javascript("jsparty/jquery/plugins/livequery/jquery.livequery.js");
				Requirements::javascript("ecommercextras/javascript/BonusProductModifier.js");
				Requirements::customScript('BonusProductModifier.set_list(new Array(0, '.implode(",", $keys).'));');
				Requirements::customScript('BonusProductModifier.set_order_item_classname("'.self::$order_item_classname.'");');
			}
			Session::set("ecommercextras.bonusitems", serialize($newBonusProductArray));
			self::$savings_calculated = true;
		}
		return self::$savings;
	}


// 					 *** database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Name = $this->LiveName();
		$this->Savings = self::workout_savings();
		$this->SerializedProductArray = serialize(self::get_bonus_product_array());
	}


}

