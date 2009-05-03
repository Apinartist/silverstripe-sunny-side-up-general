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

	static $db = array(
		'Region' => 'Text',
		'ShippingChargeType' => "Enum('Default,ForRegion')"
	);

	static $default_charge = 0;

	static function set_default_charge($defaultCharge) {
		self::$default_charge = $defaultCharge;
	}

	static $charges_by_region = array(
		'RegionA' => 10,
		'RegionB' => 20,
		'RegionC' => 30
	);

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

	static function getRegionList () {
		$keys = array_keys(self::$charges_by_region);
		return array_combine($keys,$keys);
	}

	/**
	 * This determines whether the OrderModifierForm
	 * is shown or not. {@link OrderModifier::get_form()}.
	 *
	 * @return boolean
	 */
	static function show_form() {
		return true;
	}

	/**
	 * This function returns a form that allows a user
	 * to change the modifier to the order.
	 *
	 * @param Controller $controller $controller The controller
	 * @return OrderModifierForm or subclass
	 */
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

	// Attributes Functions

	function Region() {
		if($this->Region) {
			return $this->Region;
		}
		else {
			return Session::get("RegionShippingModifierRegion");
		}
	}

	function IsDefaultCharge() {
		return $this->ID ? $this->ShippingChargeType == 'Default' : $this->LiveIsDefaultCharge();
	}

	protected function LiveRegion() {
		return $this->Region();
	}

	protected function LiveIsDefaultCharge() {
		return !array_key_exists($this->LiveRegion(), self::$charges_by_region);
	}

	/**
	 * Find the amount for the shipping on the shipping region for the order.
	 */
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

	// Display Functions

	function ShowInCart() {
		return $this->Total() > 0;
	}

	/**
	 * @TODO Add i18n entities to the text.
	 * @return string
	 */
	function TableTitle() {
		if($this->Region()) {
			$regionList = self::getRegionList();
			return "Shipping to {$regionList[$this->Region()]}";
		} else {
			return 'Shipping';
		}
	}

	/**
	 * @TODO Add i18n entities to the text.
	 * @return string
	 */
	function CartTitle() {
		return 'Shipping';
	}

	// Database Writing Function

	/*
	 * Precondition : The order item is not saved in the database yet
	 */
	function onBeforeWrite() {
		parent::onBeforeWrite();

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
