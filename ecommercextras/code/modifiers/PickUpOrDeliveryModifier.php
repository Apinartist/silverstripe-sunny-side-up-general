<?php

/**
 * PickUpOrDelivery
 * Shipping calculation scheme based on SimpleShippingModifier.
 * It lets you set fixed shipping* costs, or a fixed
 * cost for each region you're delivering to.
 *
 * @package ecommerce
 */
class PickUpOrDeliveryModifier extends OrderModifier {

// 					 *** static variables

	static $db = array(
		"Name" => "Text",
		"PickupOrDeliveryType" => "Varchar(255)",
		"TotalWeight" => "Double",
		"SerializedCalculationArray" => "Text",
		'DebugString' => 'HTMLText'

	);

	protected static $default_amount = 100;

	protected static $default_title = "Pickup Or Delivery Option Not Selected";

	protected static $pickup_options = array();

	protected static $is_chargable = true;

	protected static $total_weight = 0;

	protected static $worked_out_charges = 0;

	protected static $worked_out_charges_done = false;

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

// 					 *** static functions

	static function set_pickup_option($code = "pickup", $name = "Pick-Up", $minimumDeliveryCharge = 0, $maximumDeliveryCharge = 999999999, $minimumOrderAmountForZeroRate = 999999999, $weightMultiplier =  0 , $percentage = 0, $fixedCost = 0) {
		self::$pickup_options[$code] = array(
			"Code" => $code,
			"Name" => $name,
			"MinimumDeliveryCharge" => $minimumDeliveryCharge,
			"MaximumDeliveryCharge" => $maximumDeliveryCharge,
			"MinimumOrderAmountForZeroRate" => $minimumOrderAmountForZeroRate,
			"WeightMultiplier" => $weightMultiplier,
			"Percentage" => $percentage,
			"FixedCost" => $fixedCost
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
		Requirements::javascript("ecommercextras/javascript/AjaxOrder.js");
		Requirements::javascript("jsparty/jquery/plugins/livequery/jquery.livequery.js");
		Requirements::javascript("ecommercextras/javascript/PickUpOrDeliveryModifier.js");
		Requirements::javascript("jsparty/jquery/plugins/form/jquery.form.js");
		$fields = new FieldSet();
		$options = self::getOptionListForDropDown();
		$fields->push(new HeaderField('PickupOrDeliveryTypeHeader', 'Pick-up / Deliver'));
		$fields->push(new DropdownField('PickupOrDeliveryType','Preference',$options, Session::get("PickUpOrDeliveryOption")));
		$validator = null;
		$actions = new FieldSet(
			new FormAction_WithoutLabel('processOrderModifier', 'Update Pickup / Delivery Option')
		);
		return new PickUpOrDeliveryModifier_Form($controller, 'ModifierForm', $fields, $actions, $validator);
	}

	private static function getOptionListForDropDown() {
		$array = array();
		foreach(self::$pickup_options as $option) {
			$array[$option["Code"]] = $option["Name"];
		}
		return $array;
	}

	private $debugMessage = "";

	function __construct() {
		parent::__construct();
		if($this->getOption()) {
			if(count(self::$pickup_options)) {
				$i = 0;
				foreach(self::$pickup_options as $key => $option) {
					if($i > 0) {
						break;
					}
					$this->setOption($option["Code"]);
					$i++;
				}
			}
		}
	}

// 					 *** display
	function ShowInTable() {
		return true;
	}
	function CanRemove() {
		return false;
	}

// 					 *** inclusive / exclusive
// 					 *** other attribute: Pickup or Delivery Type
	function PickupOrDeliveryType() {
		if($this->PickupOrDeliveryType) {
			return $this->PickupOrDeliveryType;
		}
		else {
			if($v = Session::get("PickUpOrDeliveryOption")) {
				return $v;
			}
			else {
				$firstArray = array_shift(self::$pickup_options);
				return $firstArray["Code"];
			}
		}
	}

	function PickupOrDeliveryTypeArray() {
		if($this->ID) {
			$array = unserialize($this->SerializedCalculationArray);
			if(is_array($array)) {
				return $array;
			}
		}
		$currentOption = $this->PickupOrDeliveryType();
		if($currentOption) {
			if(isset(self::$pickup_options[$currentOption])) {
				return self::$pickup_options[$currentOption];
			}
		}
		return false;
	}


// 					 *** rate
// 					 *** table values
	function TableValue() {
		return "$".number_format(abs($this->Amount()), 2);
	}

	function LiveAmount () {
		return $this->Charge();
	}

// 					 *** table titles
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

// 					 *** calculations
	protected function SubTotalAmount() {
		$order = $this->Order();
		return $order->SubTotal();
	}

	function Charge() {
		$amount = 0;
		if(!self::$worked_out_charges_done) {
			self::$worked_out_charges = self::$default_amount;
			if(ShoppingCart::get_items()) {
				$amount = $this->SubTotalAmount();
				if(($amount-0) == 0){
					self::$worked_out_charges = 0;
					$this->debugMessage .= "<hr />sub total amount is 0";
				}
				else {
					$currentOptionArray = $this->PickupOrDeliveryTypeArray();
					if( is_array($currentOptionArray)) {
						// no need to charge, order is big enough
						if(floatval($currentOptionArray["MinimumOrderAmountForZeroRate"]) < floatval($amount)) {
							self::$worked_out_charges =  0;
							$this->debugMessage .= "<hr />MinimumOrderAmountForZeroRate is lower than amount".self::$worked_out_charges;
						}
						else {
							// add weight based shipping
							if($this->totalWeight() && $currentOptionArray["weightMultiplier"] ) {
								self::$worked_out_charges += $this->totalWeight() * $currentOptionArray["weightMultiplier"];
								$this->debugMessage .= "<hr />weight".self::$worked_out_charges;
							}
							// add percentage
							if($currentOptionArray["Percentage"]) {
								self::$worked_out_charges += $amount * $currentOptionArray["Percentage"];
								$this->debugMessage .= "<hr />percentage".self::$worked_out_charges;
							}
							// add fixed price
							if($currentOptionArray["FixedCost"]) {
								self::$worked_out_charges += $currentOptionArray["FixedCost"];
								$this->debugMessage .= "<hr />fixed".self::$worked_out_charges;
							}
							//is it enough?
							if(self::$worked_out_charges < $currentOptionArray["MinimumDeliveryCharge"]) {
								self::$worked_out_charges = $currentOptionArray["MinimumDeliveryCharge"];
								$this->debugMessage .= "<hr />too little".self::$worked_out_charges;
							}
							// is it too much
							if(self::$worked_out_charges > $currentOptionArray["MaximumDeliveryCharge"]) {
								self::$worked_out_charges = $currentOptionArray["MaximumDeliveryCharge"];
								$this->debugMessage .= "<hr />too much".self::$worked_out_charges;
							}
						}
					}
					else {
						//do nothing
						$this->debugMessage .= "<hr />default";
					}
				}
			}
			else {
				self::$worked_out_charges = 0;
				$this->debugMessage .= "<hr />no action";
			}
			self::$worked_out_charges_done = true;
		}
		$this->debugMessage .= "<hr />final score: ".self::$worked_out_charges;
		echo "---265".Session::get("PickUpOrDeliveryOption");
		return self::$worked_out_charges;

	}

	function Amount() {
		$this->debugMessage .= $this->LiveAmount();
		if($this->ID && $this->Amount) {
			$this->debugMessage .= "<hr />value from database";
			return $this->Amount;
		}
		elseif(self::$worked_out_charges_done) {
			$this->debugMessage .= "<hr />working out amount";
			return $this->LiveAmount();
		}
		else {
			$this->debugMessage .= "<hr />default amount";
			return self::$default_amount;
		}
	}


	protected function totalWeight() {
		if(!self::$total_weight) {
			if($this->ID) {
				return $this->TotalWeight;
			}
			else {
				$order = $this->Order();
				$orderItems = $order->Items();
				// Calculate the total weight of the order
				if($orderItems) {
					foreach($orderItems as $orderItem) {
						if(!empty($orderItem->Weight)) {
							self::$total_weight += ($orderItem->Weight+0) * $orderItem->quantity;
						}
					}
				}
			}
		}
		self::$worked_out_charges_done = true;
		return self::$total_weight;
	}

// 					 *** database functions
	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Name = $this->LiveName();
		$this->PickupOrDeliveryType = $this->PickupOrDeliveryType();
		$this->TotalWeight = $this->totalWeight();
		$this->SerializedCalculationArray = serialize($this->PickupOrDeliveryTypeArray());
		$this->DebugString = $this->debugMessage;
	}

	function setOption($type) {
		Session::set("PickUpOrDeliveryOption", $type);
		$this->PickupOrDeliveryType = $type;
	}

	function getOption() {
		return $this->PickUpOrDeliveryType();
	}


}

class PickUpOrDeliveryModifier_Form extends OrderModifierForm {

	protected static $ajaxcart_template_name = "AjaxCheckoutCart";

	static function set_ajaxcart_template_name($v) {
		self::$ajaxcart_template_name = $v;
	}


	public function processOrderModifier($data, $form) {
		$order = ShoppingCart::current_order();
		$modifiers = $order->Modifiers();
		foreach($modifiers as $modifier) {
			if (get_class($modifier) == 'PickUpOrDeliveryModifier') {
				echo "---345"."setting option";
				$modifier->setOption($data['PickupOrDeliveryType']);
				echo "---347".Session::get("PickupOrDeliveryType"]);
				$modifier->write();
			}
		}
		//Order::save_current_order();
		if(Director::is_ajax()) {
			return $this->controller->renderWith(self::$ajaxcart_template_name);
		}
		else {
			Director::redirect(CheckoutPage::find_link());
		}
		return;
	}
}
