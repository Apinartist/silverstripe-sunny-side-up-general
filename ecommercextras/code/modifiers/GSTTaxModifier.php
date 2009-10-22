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
		'TaxType' => "Enum('Exclusive, Inclusive','Exclusive')",
		'DebugString' => 'HTMLText'
	);

	protected static $default_country_code = "NZ";
		static function set_default_country_code($v) {self::$default_country_code = $v;}

	protected static $exclusive_explanation = " (added to the above price) ";
		static function set_exclusive_explanation($v) {self::$exclusive_explanation = $v;}

	protected static $inclusive_explanation = " (included in the above price) ";
		static function set_inclusive_explanation($v) {self::$inclusive_explanation = $v;}

	protected static $based_on_country_note = " - based on a sale to: ";
		static function set_based_on_country_note($v) {self::$based_on_country_note = $v;}

	protected static $no_tax_description = "tax-exempt";
		static function set_no_tax_description($v) {self::$no_tax_description = $v;}

	private static $current_country_code = "";






//-------------------------------------------------------------------- *** static functions

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
		if($obj = $this->LiveTaxObject()) {
			$obj->InclusiveOrExclusive;
		}
		else {
			return false;
		}
	}

//--------------------------------------------------------------------*** other attribute functions: country

	protected function LiveCountry() {
		if($fixeCode = Session::get("GSTTaxModifier_CountryCode")) {
			self::$current_country_code = $fixeCode;
		}
		if(!self::$current_country_code) {
			self::$current_country_code = parent::LiveCountry();
			if(!self::$current_country_code) {
				self::$current_country_code = ShoppingCart::get_country();
				if(!self::$current_country_code)
				self::$current_country_code	 = self::$default_country_code;
			}
		}
		$this->debugMessage = "<hr />Live Country Code: ".self::$current_country_code;
		return self::$current_country_code;
	}

	function LiveTaxObject() {
		if($countryCode = $this->LiveCountry()) {
			$this->debugMessage = "<hr />There is a current live tax object";
			return DataObject::get_one("GSTTaxModifierOptions", '`CountryCode` = "'.$countryCode.'"');
		}
		else {
			$this->debugMessage = "There is no current live tax object";
		}
	}

//--------------------------------------------------------------------*** rates functions

	protected function LiveRate() {
		$taxObject = $this->LiveTaxObject();

		if($taxObject) {
			$this->debugMessage .= "<hr />using rate: ".$taxObject->Rate;
			return $taxObject->Rate;
		}
		else {
			$this->debugMessage .= "<hr />no rate found: ";
			return 0;
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
		$start = '';
		$name = '';
		$end = '';
		$taxObject = $this->LiveTaxObject();
		if($taxObject) {
			$name = $taxObject->Name;
			if($rate = $this->Rate()) {
				$startString = number_format($this->Rate() * 100, 2) . '% ';
			}
			if( $this->IsExclusive()) {
				$endString = self::$exclusive_explanation;
			}
			else {
				$endString = self::$inclusive_explanation;
			}
			$countryCode = $taxObject->CountryCode;
			if(isset($_REQUEST["debug"])) {
				echo $this->debugMessage;
			}
			if($name && $rate) {
				$finalString = $startString.$name.$endString;
			}
		}
		else {
			$finalString = self::$no_tax_description;
			$countryCode = $this->LiveCountry();
		}
		if($countryCode && $finalString) {
			$countryName = Geoip::countryCode2name($countryCode);
			if(self::$based_on_country_note && $countryName  && $countryCode != self::$default_country_code) {
				$finalString .= self::$based_on_country_note.$countryName;
			}
		}
		return $finalString;
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
		$js[] = array('id' => $this->CartTotalID(), 'parameter' => 'innerHTML', 'value' => $this->Charge());
		$js[] = array('id' => $this->TableTotalID(), 'parameter' => 'innerHTML', 'value' => $this->TableValue());
		$js[] = array('id' => $this->TableTitleID(), 'parameter' => 'innerHTML', 'value' => $this->TableTitle());
	}
}













