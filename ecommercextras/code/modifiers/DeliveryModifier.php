<?php

/**
 * example of modifier
 * minimum charge $3.00
 * 2.5% over $100
 */
class DeliveryModifier extends OrderModifier {

	static $db = array(
		'Rate' => 'Decimal',
		'Name' => 'Text',
	);

	protected static $is_chargable = true;


	static $minimum_amount;

	static $percentage_rate;

	static $description;

	static function set_minimum_amount($amount) {
		self::$minimum_amount = $amount + 0;
	}

	static function set_percentage_rate($percentage) {
		self::$percentage_rate = $percentage + 0;
	}

	static function set_description($description) {
		self::$description = $description;
	}

	function Total () {
		return $this->ID ? $this->Amount : $this->Charge(); //$this->Amount :
	}
  // Attributes Functions
	function Rate() {
		return $this->ID ? $this->Rate : $this->LiveRate();
	}

	function Name() {
		return $this->ID ? $this->Name : $this->LiveName();
	}

	function IsExclusive() {
		return $this->ID ? $this->TaxType == 'Exclusive' : $this->LiveIsExclusive();
	}


	protected function LiveRate() {
		return self::$percentage_rate;
	}

	protected function LiveName() {
		return self::$description;
	}

	function LiveAmount() {
		return $this->Charge();
	}



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

	// Display Functions

	function ShowInTable() {
		return $this->Rate();
	}

	/*
	 * Precondition : Their is a rate
	 */
	function TableTitle() {
		return $this->Name();
	}

	// Database Writing Function

	/*
	 * Precondition : The order item is not saved in the database yet
	 */
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Rate = $this->LiveRate();
		$this->Name = $this->LiveName();
	}
}

?>