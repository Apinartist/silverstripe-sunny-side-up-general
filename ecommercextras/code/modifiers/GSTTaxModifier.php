<?php

/*
IsExclusive -> LiveIsExclusive
*/

class GSTTaxModifier extends OrderModifier {

// 					 *** static variables
	static $db = array(
		'Country' => 'Text',
		'Rate' => 'Double',
		'TableValue' => 'Double',
		'Name' => 'Text',
		'TaxType' => "Enum('Exclusive,Inclusive')",
		'DebugString' => 'HTMLText'
	);

	protected static $names_by_country = array("NZ" => "GST");

	protected static $rates_by_country = array("NZ" => 0.125);

	protected static $excl_by_country = array("NZ" => "Inclusive");

	protected static $default_is_exclusive = false;

	protected static $default_rate = 0.125;

	protected static $default_name = "Goods and Services Tax (GST)";

	private static $current_country_code = "";

// 					 *** static functions

	/**
	 * Set the tax information for a particular country.
	 * By default, no tax is charged.
	 *
	 * @param $country string The two-letter country code
	 * @param $rate float The tax rate, eg, 0.125 = 12.5%
	 * @param $name string The name to give to the tax, eg, "GST"
	 * @param $inclexcl string "inclusive" if the prices are tax-inclusive.
	 * 						"exclusive" if tax should be added to the order total.
	 */

	static function set_for_country($country = "NZ", $rate = 0.125, $name = "GST", $inclexcl = "Inclusive") {
		self::$names_by_country[$country] = $name;
		self::$rates_by_country[$country] = $rate;
		switch($inclexcl) {
			case 'Inclusive' : self::$excl_by_country[$country] = false; break;
			case 'Exclusive' : self::$excl_by_country[$country] = true; break;
			default: user_error("GSTTaxModifier::set_for_country - bad argument '$inclexcl' for \$inclexl.  Must be 'inclusive' or 'exclusive'.", E_USER_ERROR);
		}
	}

	static function set_default_is_exclusive($boolean) {
		self::$default_is_exclusive = $boolean;
	}

	static function set_default_rate($v) {
		self::$default_rate = $v;
	}

// 					 *** debug
	var $debugMessage = '';

// 					 *** display functions
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return true;
	}

// 					 *** inclusive / exclusive function
	function IsExclusive() {
		return $this->ID ? $this->TaxType == 'Exclusive' : $this->LiveIsExclusive();
	}

	protected function LiveIsExclusive() {
		$countryCode = $this->LiveCountry();
		if(isset(self::$excl_by_country[$countryCode])) {
			return self::$excl_by_country[$countryCode];
		}
		else {
			return self::$default_is_exclusive;
		}
	}

// 					 *** other attribute functions: country
	function Country() {
		return $this->ID ? $this->Country : $this->LiveCountry();
	}

	protected function LiveCountry() {
		if(!self::$current_country_code) {
			self::$current_country_code = EcommerceRole::findCountry();
		}
		return self::$current_country_code;
	}

// 					 *** rates functions
	function Rate() {
		return $this->ID ? $this->Rate : $this->LiveRate();
	}

	protected function LiveRate() {
		$countryCode = $this->LiveCountry();
		$this->debugMessage .= "<hr />country code: ".$countryCode;
		if(isset(self::$rates_by_country[$countryCode])) {
			//$this->debugMessage .= "<hr />using rate: ".self::$rates_by_country[$countryCode];
			return self::$rates_by_country[$countryCode];
		}
		else {
			$this->debugMessage .= "<hr />using default rate: ".self::$default_rate;
			return self::$default_rate;
		}
	}


// 					 *** table value functions
// note that this talks about AddedCharge, which can actually be zero while the table shows a value (inclusive case).
	function LiveAmount() {
		return $this->AddedCharge();
	}

	function TableAmount() {
		return $this->Charge();
	}

	function TableValue() {
		return "$".number_format(abs($this->Charge()), 2);
	}

// 					 *** title function
	function Name() {
		return $this->ID ? $this->Name : $this->LiveName();
	}

	protected function LiveName() {
		$countryCode = $this->LiveCountry();
		$rate = $this->Rate();
		$start = '';
		$name = '';
		$end = '';
		if(isset(self::$names_by_country[$countryCode])) {
			$name = self::$names_by_country[$countryCode];
		}
		else {
			$name = self::$default_name;
		}
		if($rate) {
			$start = number_format($this->Rate() * 100, 2) . '% ';
		}
		if( $this->IsExclusive() || ! $rate) {
			$end = '';
		}
		else {
			$end = ' (included in the above price) ';
		}
		return $start.$name.$end;
	}


	function TableTitle() {
		return $this->Name();
	}


// 					 *** calculations
	/**
	 * Get the tax amount that needs to be added to the given order.
	 * If tax is inclusive, then this will be 0
	 */
	function AddedCharge() {
		return $this->IsExclusive() ? $this->Charge() : 0;
	}

	/**
	 * Get the tax amount on the given order.
	 */
	function Charge() {
		// Exclusive is easy
		// Inclusive is harder. For instance, with GST the tax amount is 1/9 of the inclusive price, not 1/8
		return $this->TaxableAmount() * ($this->IsExclusive() ? $this->Rate() : (1 - (1 / (1 + $this->Rate()))));
	}

	function TaxableAmount() {
		$order = $this->Order();
		return $order->SubTotal() + $order->ModifiersSubTotal(array("GSTTaxModifier"));
	}



// 					 *** database
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Amount = $this->Charge();
		$this->Country = $this->LiveCountry();
		$this->Rate = $this->LiveRate();
		$this->TableValue = $this->TableValue();
		$this->TaxType = $this->LiveIsExclusive() ? 'Exclusive' : 'Inclusive';
		$this->DebugString = $this->debugMessage;
	}

// 					 *** ajax
	function updateForAjax(array &$js) {
		$amount = $this->Charge();
		$js[] = array('id' => $this->CartTotalID(), 'parameter' => 'innerHTML', 'value' => $amount);
		$js[] = array('id' => $this->TableTotalID(), 'parameter' => 'innerHTML', 'value' => $this->TableValue());
		$js[] = array('id' => $this->TableTitleID(), 'parameter' => 'innerHTML', 'value' => $this->TableTitle());
	}
}

