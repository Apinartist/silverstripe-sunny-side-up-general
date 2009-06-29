<?php

/**
 * RegionShippingModifier
 * Shipping calculation scheme based on SimpleShippingModifier.
 * It lets you set fixed shipping* costs, or a fixed
 * cost for each region you're delivering to.
 *
 * @package ecommerce
 */
class RegionShippingModifier extends OrderModifier {


// static variables \\\

	static $db = array(
		'Name' => 'Text',
		'ShippingChargeType' => "Enum('Default,ForRegion')"
	);

	static $default_charge = 0;

	static $charges_by_region = array(
		'RegionA' => 10,
		'RegionB' => 20,
		'RegionC' => 30
	);


// static functions \\\
	/**
	 * Set shipping charges on a region by region basis.
	 * For example, SimpleShippingModifier::set_charges_for_regions(array(
	 *   'US' => 10,
	 *   'NZ' => 5,
	 * ));
	 * @param regionMap A map of 2-letter region codes
	 */
	static function set_charges_for_regions($regionMap) {
		self::$charges_by_region = array_merge(self::$charges_by_region, $regionMap);
	}

	static function set_default_charge($defaultCharge) {
		self::$default_charge = $defaultCharge;
	}


	static function getRegionList () {
		$keys = array_keys(self::$charges_by_region);
		return array_combine($keys,$keys);
	}

	static function show_form() {
		return true;
	}

	static function get_form($controller) {
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript("mysite/javascript/RegionShippingModifier.js");
		Requirements::css("mysite/css/RegionShippingModifier.css");
		$fields = new FieldSet();
		$regionList = self::getRegionList();
		$fields->push($regionField = new DropdownField('Region','Shipping Region',$regionList, Session::get("RegionShippingModifierRegion")));
		$validator = null;

		$actions = new FieldSet(
			new FormAction_WithoutLabel('processOrderModifier', 'Update shipping region')
		);

		return new RegionShippingModifier_Form($controller, 'ModifierForm', $fields, $actions, $validator);
	}

// display functions \\\
	function ShowInTable() {
		return true;
	}
	function CanRemove() {
		function false;
	}

// other attributes: region \\\
	function Region() {
		if($this->Region) {
			return $this->Region;
		}
		else {
			return Session::get("RegionShippingModifierRegion");
		}
	}

	protected function LiveRegion() {
		return $this->Region();
	}
// other attributes: default charge\\\
	function IsDefaultCharge() {
		return $this->ID ? $this->ShippingChargeType == 'Default' : $this->LiveIsDefaultCharge();
	}

	protected function LiveIsDefaultCharge() {
		return !array_key_exists($this->LiveRegion(), self::$charges_by_region);
	}

	/**
	 * Find the amount for the shipping on the shipping region for the order.
	 */
// table values \\\
	function LiveAmount() {
		if(!ShoppingCart::get_items()) {
			return 0;
		}
		else {
			$qty = 0;
			$items = ShoppingCart::get_items();
			foreach($items as $item) {
				$qty += $item->Quantity;
			}
			$amount = $this->LiveIsDefaultCharge() ? self::$default_charge : self::$charges_by_region[$this->LiveRegion()];
			return $amount * $qty;
		}
	}

	function TableValue() {
		return "$".number_format(abs($this->Amount()), 2);
	}

//table titles \\\
	function LiveName() {
		if($this->Region()) {
			$regionList = self::getRegionList();
			return "Shipping to {$regionList[$this->Region()]}";
		} else {
			return 'Shipping';
		}
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


// database function \\\
	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Name = $this->Region();
		//$this->Region = $this->LiveRegion();
		$this->ShippingChargeType = $this->LiveIsDefaultCharge() ? 'Default' : 'ForRegion';
	}
}

class RegionShippingModifier_Form extends OrderModifierForm {
	public function processOrderModifier($data, $form) {
		$order = ShoppingCart::current_order();
		$modifiers = $order->Modifiers();
		foreach($modifiers as $modifier) {
			if (get_class($modifier) == 'RegionShippingModifier') {
				Session::set("RegionShippingModifierRegion", $data['Region']);
				$modifier->Region = $data['Region'];
				//ShoppingCart::add_new_modifier($modifier);
			}
		}
		Order::save_current_order();
		Director::redirect(CheckoutPage::find_link());
		return;
	}
}
