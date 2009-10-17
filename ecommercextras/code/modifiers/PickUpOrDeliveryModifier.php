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
		"SerializedCalculationObject" => "Text",
		'DebugString' => 'HTMLText'

	);

	protected static $is_chargable = true;

	protected static $total_weight = 0;

	protected static $actual_charges = 0;

	protected static $calculations_done = false;

//--------------------------------------------------------------------*** static functions



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
			$defaultValue = PickUpOrDeliveryModifierData::default_object()->ID;
		}

		$fields = new FieldSet();
		$options = self::getOptionListForDropDown();
		$fields->push(new HeaderField('PickupOrDeliveryTypeHeader', 'Pick-up / Deliver'));

		$fields->push(new DropdownField('PickupOrDeliveryType','Preference',$options, $defaultValue);
		$validator = null;
		$actions = new FieldSet(
			new FormAction_WithoutLabel('processOrderModifier', 'Update Pickup / Delivery Option')
		);
		return new PickUpOrDeliveryModifier_Form($controller, 'ModifierForm', $fields, $actions, $validator);
	}

	private static function getOptionListForDropDown() {
		$array = array();
		$options = DataObject::get("PickUpOrDeliveryModifierData");
		if($options) {
			foreach($options as $option) {
				$array[$option->Code] = $option->Name;
			}
		}
		else {
			$array[0] = "No pick-up or delivery options available";
		}
		return $array;
	}

//-------------------------------------------------------------------- *** internal variables

	protected $debugMessage = "";

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
			$option = PickUpOrDeliveryModifierData::default_object();
			$code = $option->Code;
			$this->setOption($code);
			return $code;
		}
	}

	function PickupOrDeliveryTypeObject() {
		if($this->ID) {
			$object = unserialize($this->SerializedCalculationObject);
			if(is_object($object)) {
				return $object;
			}
		}
		$currentOption = $this->LivePickupOrDeliveryType();
		if($currentOption) {
			DataObject::get_one("PickUpOrDeliveryModifierData", $filter = '`Code` = "'.$currentOption.'"');
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

//--------------------------------------------------------------------*** table titles
	function LiveName() {
		$obj = $this->PickupOrDeliveryTypeObject();
		if(is_object($obj)) {
			return $obj->Name;
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
					$obj = $this->PickupOrDeliveryTypeObject();
					if( is_object($obj)) {
						// no need to charge, order is big enough
						if(floatval($obj->MinimumOrderAmountForZeroRate) < floatval($amount)) {
							self::$actual_charges =  0;
							$this->debugMessage .= "<hr />MinimumOrderAmountForZeroRate is lower than amount".self::$actual_charges;
						}
						else {
							// add weight based shipping
							if($this->totalWeight() && $obj->weightMultiplier ) {
								self::$actual_charges += $this->totalWeight() * $obj->weightMultiplier;
								$this->debugMessage .= "<hr />weight".self::$actual_charges;
							}
							// add percentage
							if($obj->Percentage) {
								self::$actual_charges += $amount * $obj->Percentage;
								$this->debugMessage .= "<hr />percentage".self::$actual_charges;
							}
							// add fixed price
							if($obj->FixedCost) {
								self::$actual_charges += $obj->FixedCost;
								$this->debugMessage .= "<hr />fixed".self::$actual_charges;
							}
							//is it enough?
							if(self::$actual_charges < $obj->MinimumDeliveryCharge) {
								self::$actual_charges = $obj->MinimumDeliveryCharge;
								$this->debugMessage .= "<hr />too little".self::$actual_charges;
							}
							// is it too much
							if(self::$actual_charges > $obj->MaximumDeliveryCharge) {
								self::$actual_charges = $obj->MaximumDeliveryCharge;
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
		$this->SerializedCalculationObject = serialize($this->PickupOrDeliveryTypeObject());
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

