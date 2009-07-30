<?php

/**
 * example of modifier
 * minimum charge $3.00
 * 2.5% over $100
 */
class DeliveryModifier extends OrderModifier {

// 					 *** static variables
	static $db = array(
		'Rate' => 'Double',
		'Name' => 'Text',
	);

	protected static $is_chargable = true;

	protected static $minimum_amount;

	protected static $percentage_rate;

	protected static $description;

// 					 *** static functions
	static function set_minimum_amount($amount) {
		self::$minimum_amount = $amount + 0;
	}

	static function set_percentage_rate($percentage) {
		self::$percentage_rate = $percentage + 0;
	}

	static function set_description($description) {
		self::$description = $description;
	}

// 					 *** display function
	function ShowInTable() {
		return true;
	}

// 					 *** inclusive / exclusive functions
	function IsExclusive() {
		return $this->ID ? $this->TaxType == 'Exclusive' : $this->LiveIsExclusive();
	}

	function CanRemove () {
		return false;
	}
// 					 *** rate functions
	function Rate() {
		return $this->ID ? $this->Rate : $this->LiveRate();
	}
	protected function LiveRate() {
		return self::$percentage_rate;
	}

// 					 *** table value functions
	function Total () {
		return $this->ID ? $this->Amount : $this->Charge(); //$this->Amount :
	}

	function LiveAmount() {
		return $this->Charge();
	}

	function TableValue() {
		return "$".number_format(abs($this->Amount()), 2);
	}

// 					 *** table title
	protected function LiveName() {
		return self::$description;
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
	function Charge() {
		$SubTotalAmount = $this->SubTotalAmount();
		$charge = $SubTotalAmount * $this->Rate();
		if($charge < self::$minimum_amount) {
			return self::$minimum_amount;
		}
		return $charge;
	}

	function SubTotalAmount() {
		$order = $this->Order();
		return $order->SubTotal();
	}


// 					 *** database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Rate = $this->LiveRate();
		$this->Name = $this->LiveName();
	}
}

