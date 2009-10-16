<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description: Shipping calculation scheme based on SimpleShippingModifier.
 * It lets you set fixed shipping costs, or a fixed
 * cost for each region you're delivering to.
 */
class PickUpOrDeliveryModifier extends OrderModifier {

//--------------------------------------------------------------------*** static variables

	static $db = array(
		"Name" => "Text",
		"PickupOrDeliveryType" => "Varchar(255)",
		"TotalWeight" => "Double",
		"SerializedCalculationArray" => "Text",
		'DebugString' => 'HTMLText'

	);

	protected static $default_code = "";
	static function set_default_code($v) {self::$default_code = $v;}


	protected static $pickup_options = array();

	protected static $is_chargable = true;

	protected static $total_weight = 0;

	protected static $actual_charges = 0;

	protected static $calculations_done = false;

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

//--------------------------------------------------------------------*** static functions

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


	static function show_form() {
		$items = ShoppingCart::get_items();
		if(count($items)) {
			return true;
		}
		return false;
	}

	static function get_form($controller) {
		Requirements::javascript("ecommercextras/javascript/PickUpOrDeliveryModifier.js");
		Requirements::javascript("jsparty/jquery/plugins/form/jquery.form.js");
		Requirements::javascript("ecommercextras/javascript/AjaxCheckout.js");

		if($defaultValue = Session::get("PickUpOrDeliveryOption")) {
			$defaultValue = $v;
		}
		else {
			$defaultValue = self::$default_code;
		}

		$fields = new FieldSet();
		$options = self::getOptionListForDropDown();
		$fields->push(new HeaderField('PickupOrDeliveryTypeHeader', 'Pick-up / Deliver'));

		$fields->push(new DropdownField('PickupOrDeliveryType','Preference',$options, $defaultValue, $form = null, $emptyOption = "--- pick option ---"));
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


//-------------------------------------------------------------------- *** display
	function ShowInTable() {
		return true;
	}
	function CanRemove() {
		return false;
	}

// --------------------------------------------------------------------*** other attribute: Pickup or Delivery Type
	function LivePickupOrDeliveryType() {
		if($v = Session::get("LivePickupOrDeliveryOption")) {
			return $v;
		}
		if($this->PickupOrDeliveryType) {
			return $this->PickupOrDeliveryType;
		}
		else {
			if(isset(self::$pickup_options[self::$default_code])) {
				$array = self::$pickup_options[self::$default_code];
			}
			else {
				$array = current(self::$pickup_options);
			}
			$code = $array["Code"];
			$this->setOption($code);
			return $code;
		}
	}

	function PickupOrDeliveryTypeArray() {
		if($this->ID) {
			$array = unserialize($this->SerializedCalculationArray);
			if(is_array($array)) {
				return $array;
			}
		}
		$currentOption = $this->LivePickupOrDeliveryType();
		if($currentOption) {
			if(isset(self::$pickup_options[$currentOption])) {
				return self::$pickup_options[$currentOption];
			}
		}
		return false;
	}


//-------------------------------------------------------------------- *** table values
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

//-------------------------------------------------------------------- *** calculations
	protected function SubTotalAmount() {
		$order = $this->Order();
		return $order->SubTotal();
	}

	function Charge() {
		$amount = 0;
		if(!self::$calculations_done) {
			self::$actual_charges = 0;
			if(ShoppingCart::get_items()) {
				$amount = $this->SubTotalAmount();
				if(($amount-0) == 0){
					self::$actual_charges = 0;
					$this->debugMessage .= "<hr />sub total amount is 0";
				}
				else {
					$currentOptionArray = $this->PickupOrDeliveryTypeArray();
					if( is_array($currentOptionArray)) {
						// no need to charge, order is big enough
						if(floatval($currentOptionArray["MinimumOrderAmountForZeroRate"]) < floatval($amount)) {
							self::$actual_charges =  0;
							$this->debugMessage .= "<hr />MinimumOrderAmountForZeroRate is lower than amount".self::$actual_charges;
						}
						else {
							// add weight based shipping
							if($this->totalWeight() && $currentOptionArray["weightMultiplier"] ) {
								self::$actual_charges += $this->totalWeight() * $currentOptionArray["weightMultiplier"];
								$this->debugMessage .= "<hr />weight".self::$actual_charges;
							}
							// add percentage
							if($currentOptionArray["Percentage"]) {
								self::$actual_charges += $amount * $currentOptionArray["Percentage"];
								$this->debugMessage .= "<hr />percentage".self::$actual_charges;
							}
							// add fixed price
							if($currentOptionArray["FixedCost"]) {
								self::$actual_charges += $currentOptionArray["FixedCost"];
								$this->debugMessage .= "<hr />fixed".self::$actual_charges;
							}
							//is it enough?
							if(self::$actual_charges < $currentOptionArray["MinimumDeliveryCharge"]) {
								self::$actual_charges = $currentOptionArray["MinimumDeliveryCharge"];
								$this->debugMessage .= "<hr />too little".self::$actual_charges;
							}
							// is it too much
							if(self::$actual_charges > $currentOptionArray["MaximumDeliveryCharge"]) {
								self::$actual_charges = $currentOptionArray["MaximumDeliveryCharge"];
								$this->debugMessage .= "<hr />too much".self::$actual_charges;
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
				self::$actual_charges = 0;
				$this->debugMessage .= "<hr />no action";
			}
			self::$calculations_done = true;
		}
		$this->debugMessage .= "<hr />final score: ".self::$actual_charges;
		return self::$actual_charges;

	}

	function Amount() {
		$this->debugMessage .= $this->LiveAmount();
		if($this->ID && $this->Amount) {
			$this->debugMessage .= "<hr />value from database";
			return $this->Amount;
		}
		elseif(self::$calculations_done) {
			$this->debugMessage .= "<hr />working out amount";
			return $this->LiveAmount();
		}
		else {
			$this->debugMessage .= "<hr />default amount";
			return $this->Charge();
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
		self::$calculations_done = true;
		return self::$total_weight;
	}

//-------------------------------------------------------------------- *** database functions
	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Name = $this->LiveName();
		$this->PickupOrDeliveryType = $this->LivePickupOrDeliveryType();
		$this->TotalWeight = $this->totalWeight();
		$this->SerializedCalculationArray = serialize($this->PickupOrDeliveryTypeArray());
		$this->DebugString = $this->debugMessage;
	}

//-------------------------------------------------------------------- *** getter and setters

	function setOption($type) {
		//Session::set("testme", "abc");
		Session::set("LivePickupOrDeliveryOption", $type);
		$this->PickupOrDeliveryType = $type;
	}

	function getOption() {
		return $this->LivePickUpOrDeliveryType();
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
				$modifier->setOption($data['PickupOrDeliveryType']);
				$modifier->write();
			}
		}
		Order::save_current_order();
		if(Director::is_ajax()) {
			return ShoppingCartExtension_Controller::modifier_json_code();
		}
		else {
			Director::redirect(CheckoutPage::find_link());
		}
		return;
	}
}

function class PickUpOrDeliveryModifier_data extends DataObject {


}

