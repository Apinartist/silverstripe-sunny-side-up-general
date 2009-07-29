<?php

/**
 * adds one or more bonus products
 * the product page / dataobject need to have a function BonusProductsForCart
 * which returns an array of IDs
 * @package ecommerce
 * each time a bonus item is added, it is added to Session::set("ecommercextras.bonusitems", $arrayofitems);
 * the following situations apply:
 * 1. item has not been added and thus will be added and qty set
 * 2. item has already been added and qty is updated
 * 3. item should be removed because parent item is no longer in cart
 */
class BonusProductModifier extends OrderModifier {

// static variables \\\

	static $db = array(
		'Name' => 'Text'
	);

	protected static $savings_calculated = false ;

	protected static $savings = 0;

	protected static $order_item_classname = "Product_OrderItem";


// static functions \\\
	static function set_order_item_classname($v) {
		self::$order_item_classname = $v;
	}

	static function show_form() {
		return self::workoutSavings();
	}

	static function get_form($controller) {
		return false;
	}


// display functions \\\
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return false;
	}

// inclusive / exclusive functions  \\\
// table values \\\
	function LiveAmount() {
		return self::workoutSavings();
	}

	function TableValue() {
		return "$".number_format(abs($this->Amount()), 2);
	}


// table titles \\\
	function LiveName() {
		self::$title;
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




// calculations \\\
	static function workoutSavings() {
		if(!self::$savings_calculated) {
			self::$is_chargable = false;
			self::$savings = 0;
			$InCartIDArray = array();
			$newBonusProductArray = array();
			$oldBonusProductArray = array();

			//get ones already added
			$serializedOldBonusProductArray= Session::get("ecommercextras.bonusitems");
			if(!$serializedOldBonusProductArray) {
				$oldBonusProductArray = array();
				//debug::show("old bonus products to not exist yet");
			}
			else {
				$oldBonusProductArray = unserialize($serializedOldBonusProductArray);
				//debug::show("already added bonus products: ".print_r($oldBonusProductArray, true));
			}
			//print_r($oldBonusProductArray);
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
									$orderItem->setAlternativeUnitPrice(0.001);
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


// database functions \\\
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Name = $this->LiveName();
	}
}

