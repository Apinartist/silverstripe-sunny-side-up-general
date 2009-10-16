<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description: allows adding  GST sales tax to order
 *
 * NOTA BENE :: NOTA BENE :: NOTA BENE :: NOTA BENE :: NOTA BENE ::
 * @important: in the order templates, change as follows:
 * FROM: <td id="$TableTotalID" class="price"><% if IsChargable %>$Amount.Nice<% else %>-$Amount.Nice<% end_if %></td>
 * TO: <td id="$TableTotalID" class="price">$TableValue</td>
 *
 */

class GSTTaxModifier extends TaxModifier {

//-------------------------------------------------------------------- *** static variables
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

	protected static $exclusive_explanation = " (excluded from the above price) ";

	protected static $inclusive_explanation = " (included in the above price) ";

	protected static $based_on_country_note = " - based on a sale to: ";

	private static $current_country_code = "";

//-------------------------------------------------------------------- *** static functions

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

	static function set_for_country($country = "NZ", $rate = 0.125, $name = "GST", $inclexcl = "Inclusive", $PriceSuffix = "") {
		self::$names_by_country[$country] = $name;
		self::$rates_by_country[$country] = $rate;
		switch($inclexcl) {
			case 'Inclusive' || 'inclusive' : self::$excl_by_country[$country] = false; break;
			case 'Exclusive' || 'exclusive' : self::$excl_by_country[$country] = true; break;
			default: user_error("GSTTaxModifier::set_for_country - bad argument '$inclexcl' for \$inclexl.  Must be 'Inclusive' or 'Exclusive'.", E_USER_ERROR);
		}
	}

	static function set_default_is_exclusive($boolean) {self::$default_is_exclusive = $boolean;}
	static function set_default_rate($v) {self::$default_rate = $v;}
	static function set_default_name($v) {self::$default_name = $v;}
	static function set_exclusive_explanation($v) {self::$exclusive_explanation = $v;}
	static function set_inclusive_explanation($v) {self::$inclusive_explanation = $v;}
	static function set_based_on_country_note($v) {self::$based_on_country_note = $v;}

	static function override_country($countryCode) {
		self::$current_country_code = $countryCode;
		Session::set("GSTTaxModifier_CountryCode", $countryCode);
	}

// -------------------------------------------------------------------- *** internal variables
	protected $debugMessage = '';

// -------------------------------------------------------------------- *** display functions
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return true;
	}

// -------------------------------------------------------------------- *** inclusive / exclusive function
	/*
	* returns boolean value true / false
	*/
	protected function LiveIsExclusive() {
		$countryCode = $this->LiveCountry();
		if(isset(self::$excl_by_country[$countryCode])) {
			return self::$excl_by_country[$countryCode];
		}
		else {
			return self::$default_is_exclusive;
		}
	}

//--------------------------------------------------------------------*** other attribute functions: country

	protected function LiveCountry() {
		if($fixeCode = Session::get("GSTTaxModifier_CountryCode")) {
			self::$current_country_code = $fixeCode;
		}
		if(!self::$current_country_code) {
			self::$current_country_code = EcommerceRole::findCountry();
			if(!self::$current_country_code) {
				self::$current_country_code = current(array_keys(self::$names_by_country));
			}
		}
		return self::$current_country_code;
	}

//--------------------------------------------------------------------*** rates functions

	protected function LiveRate() {
		$countryCode = $this->LiveCountry();
		$this->debugMessage .= "<hr />country code: ".$countryCode;
		if(isset(self::$rates_by_country[$countryCode])) {
			$this->debugMessage .= "<hr />using rate: ".self::$rates_by_country[$countryCode];
			return self::$rates_by_country[$countryCode];
		}
		else {
			$this->debugMessage .= "<hr />using default rate: ".self::$default_rate;
			return self::$default_rate;
		}
	}


//-------------------------------------------------------------------- *** table value functions
// note that this talks about AddedCharge, which can actually be zero while the table shows a value (inclusive case).

	function getAmount() {
		if($this->IsExclusive()) {
			if($this->ID) {
				return $this->Amount;
			}
			else {
				return $this->LiveAmount();
			}
		}
		return 0;
	}

	function TableAmount() {
		return $this->Charge();
	}

	function TableValue() {
		return "$".number_format(abs($this->Charge()), 2);
	}

//-------------------------------------------------------------------- *** title function

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
			$end = self::$exclusive_explanation;
		}
		else {
			$end = self::$inclusive_explanation;
		}
		if($countryName = Geoip::countryCode2name($countryCode) && self::$based_on_country_note) {
			$end .= self::$based_on_country_note.$countryName;
		}
		return $start.$name.$end;
	}


	function TableTitle() {
		return $this->Name();
	}


// -------------------------------------------------------------------- *** calculations



	function TaxableAmount() {
		$order = $this->Order();
		return $order->SubTotal() + $order->ModifiersSubTotal(array("GSTTaxModifier"));
	}



// -------------------------------------------------------------------- *** database
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Amount = $this->Charge();
		$this->Country = $this->LiveCountry();
		$this->Rate = $this->LiveRate();
		$this->TableValue = $this->TableValue();
		$this->Name = $this->LiveName();
		$this->TaxType = $this->LiveIsExclusive() ? 'Exclusive' : 'Inclusive';
		$this->DebugString = $this->debugMessage;
	}


// ajax  NEED TO OVERRIDE THE STANDARD ONE..
	function updateForAjax(array &$js) {
		$amount = $this->Charge();
		$js[] = array('id' => $this->CartTotalID(), 'parameter' => 'innerHTML', 'value' => $amount);
		$js[] = array('id' => $this->TableTotalID(), 'parameter' => 'innerHTML', 'value' => $this->TableValue());
		$js[] = array('id' => $this->TableTitleID(), 'parameter' => 'innerHTML', 'value' => $this->TableTitle());
	}

}













