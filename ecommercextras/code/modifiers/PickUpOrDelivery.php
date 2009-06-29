<?php

/**
 * PickUpOrDelivery
 * Shipping calculation scheme based on SimpleShippingModifier.
 * It lets you set fixed shipping* costs, or a fixed
 * cost for each region you're delivering to.
 *
 * @package ecommerce
 */
class PickUpOrDelivery extends OrderModifier {

//static variables \\\

	static $db = array(
		'Rate' => 'Double',
		'Name' => 'Text',
		"PickupOrDeliveryType" => "Varchar(255)"
	);

	protected static $default_amount = 100;

	protected static $default_title = "Pickup Or Delivery Option Not Selected";

	protected static $pickup_options = array();

	protected static $is_chargable = true;

	/**
	 * Set the tax information for a particular country.
	 * By default, no tax is charged.
	 *
	 * @param $code string internal code - should be alphanumeric only - no spaces!
	 * @param $name string external description
	 * @param $code float percentage of exact cost (e.g. 3.40) for delivery
	 * @param $minimum float mininum to allow this option for this order (i.e. only deliver orders to South Island if they are over $100
	 * @param $maximum float maximum order total for applying charge (i.e. if south island order is over 1000 then waive it)
	 * @param $isPercentage boolean if the cost is a percentage or a exact cost
	 */

//static functions  \\\

	static function set_pickup_option($code, $name, $cost, $minimum, $maximum, $isPercentage) {
		self::$pickup_options[$code] = array(
			"Code" => $code,
			"Name" => $name,
			"Cost" => $cost,
			"Minimum" => $minimum,
			"Maximum" => $maximum,
			"IsPercentage" => $isPercentage,
		);
	}

	static function set_default_amount($v) {
		self::$default_amount = $v;
	}

	static function set_default_title($v) {
		self::$default_title = $v;
	}

	static function show_form() {
		$items = ShoppingCart::get_items();
		if(count($items)) {
			return true;
		}
		return false;
	}

	static function get_form($controller) {
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript("ecommercextras/javascript/ajaxcart.js");
		Requirements::javascript("ecommercextras/javascript/PickUpOrDelivery.js");
		Requirements::javascript("jsparty/jquery/plugins/form/jquery.form.js");
		$fields = new FieldSet();
		$options = self::getOptionListForDropDown();
		$fields->push(new HeaderField('PickupOrDeliveryTypeHeader', 'Pick-up / Deliver'));
		$fields->push(new DropdownField('PickupOrDeliveryType','Preference',$options, Session::get("PickUpOrDeliveryOption")));
		$validator = null;
		$actions = new FieldSet(
			new FormAction_WithoutLabel('processOrderModifier', 'Update Pickup / Delivery Option')
		);
		return new PickUpOrDelivery_Form($controller, 'ModifierForm', $fields, $actions, $validator);
	}

	private static function getOptionListForDropDown() {
		$array = array();
		foreach(self::$pickup_options as $option) {
			$array[$option["Code"]] = $option["Name"];
		}
		return $array;
	}

//display  \\\
	function ShowInTable() {
		return true;
	}
	function CanRemove() {
		return false;
	}

// inclusive / exclusive  \\\
// other attribute: Pickup or Delivery Type \\\
	function PickupOrDeliveryType() {
		if($this->PickupOrDeliveryType) {
			return $this->PickupOrDeliveryType;
		}
		else {
			return Session::get("PickUpOrDeliveryOption");
		}
	}

	function PickupOrDeliveryTypeArray() {
		$currentOption = $this->PickupOrDeliveryType();
		if($currentOption) {
			if(isset(self::$pickup_options[$currentOption])) {
				return self::$pickup_options[$currentOption];
			}
		}
		return false;
	}


// rate \\\
// table values \\\
	function TableValue() {
		return "$".number_format(abs($this->Amount()), 2);
	}

	function LiveAmount () {
		$this->Charge();
	}

// table titles  \\\
	function LiveName() {
		$array = $this->PickupOrDeliveryTypeArray();
		if($array) {
			return $array["Name"];
		}
		else {
			return self::$default_title;
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

// calculations  \\\
	protected function SubTotalAmount() {
		$order = $this->Order();
		return $order->SubTotal();
	}

	function Charge() {
		$value = self::$default_amount;
		$isPercentage = false;
		if(ShoppingCart::get_items()) {
			$amount = $this->SubTotalAmount();
			$currentOptionArray = $this->PickupOrDeliveryTypeArray();
			if( is_array($currentOptionArray) && $amount) {
				$isPercentage = $currentOptionArray["IsPercentage"];
				if($currentOptionArray["Minimum"] > $amount) {
					$value =  self::$default_amount;
				}
				elseif($currentOptionArray["Maximum"] < $amount) {
					$value =  0;
				}
				else {
					$value = $currentOptionArray["Cost"];
				}
			}
		}
		else {
			return 0;
		}
		if($isPercentage && $value != 0) {
			$value = $value * $amount;
		}
		return $value;
	}

// database functions \\\
	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Name = $this->LiveName();
	}
}

class PickUpOrDelivery_Form extends OrderModifierForm {
	public function processOrderModifier($data, $form) {
		$order = ShoppingCart::current_order();
		$modifiers = $order->Modifiers();
		foreach($modifiers as $modifier) {
			if (get_class($modifier) == 'PickUpOrDelivery') {
				Session::set("PickUpOrDeliveryOption", $data['PickupOrDeliveryType']);
				$modifier->PickupOrDeliveryType = $data['PickupOrDeliveryType'];
			}
		}
		Order::save_current_order();
		if(Director::is_ajax()) {
			return $this->controller->renderWith("AjaxCheckoutCart");
		}
		else {
			Director::redirect(CheckoutPage::find_link());
		}
		return;
	}
}
