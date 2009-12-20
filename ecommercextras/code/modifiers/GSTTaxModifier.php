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

	protected static $refund_title = "Tax Exemption";
		static function set_refund_title($v) {self::$refund_title = $v;}

	protected static $no_tax_description = "tax-exempt";
		static function set_no_tax_description($v) {self::$no_tax_description = $v;}

	protected static $order_item_function_for_tax_exclusive_portion = "";//PortionWithoutTax
		static function set_order_item_function_for_tax_exclusive_portion($v) {self::$order_item_function_for_tax_exclusive_portion = $v;}

	private static $current_country_code = "";

//-------------------------------------------------------------------- *** static functions

	static function override_country($countryCode) {
		self::$current_country_code = $countryCode;
		$this->debugMessage .= "<hr />OVERRIDING COUNTRY CODE";
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
		if($fixedCode = Session::get("GSTTaxModifier_CountryCode")) {
			$this->debugMessage .= "<hr />taking session value for country code";
			self::$current_country_code = $fixedCode;
		}
		if(!self::$current_country_code) {
			$this->debugMessage .= "<hr />using shopping cart for country";
			self::$current_country_code = ShoppingCart::get_country();
			if(!self::$current_country_code) {
				self::$current_country_code = parent::LiveCountry();
				$this->debugMessage .= "<hr />using parent::LiveCountry country for country";
				if(!self::$current_country_code) {
					$this->debugMessage .= "<hr />using default country for cart";
					self::$current_country_code	 = self::$default_country_code;
				}
			}
		}
		$this->debugMessage .= "<hr />Live Country Code: ".self::$current_country_code;
		return self::$current_country_code;
	}

	function LiveTaxObject() {
		if($countryCode = $this->LiveCountry()) {
			$this->debugMessage .= "<hr />There is a current live tax object";
			return DataObject::get_one("GSTTaxModifierOptions", '`CountryCode` = "'.$countryCode.'"');
		}
		else {
			$this->debugMessage .= "<hr />There is no current live tax object";
		}
	}

	function DefaultLiveTaxObject() {
		$defaultCountryCode = GSTTaxModifierOptions::$defaults["CountryCode"];
		if($defaultCountryCode) {
			$this->debugMessage .= "<hr />There is a current live DEFAULT tax object";
			return DataObject::get_one("GSTTaxModifierOptions", '`CountryCode` = "'.$defaultCountryCode.'"');
		}
		else {
			$this->debugMessage .= "<hr />There is no current live DEFAULT tax object";
		}
	}

//--------------------------------------------------------------------*** rates functions

	protected function LiveRate() {
		if($this->IsRefundSituation()) {
			$defaultTaxObject = $this->DefaultLiveTaxObject();
			if($defaultTaxObject) {
				$this->debugMessage .= "<hr />using DEFAULT (REFUND) rate: ".$defaultTaxObject->Rate;
				return $defaultTaxObject->Rate;
			}
			else {
				$this->debugMessage .= "<hr />no DEFAULT (REFUND) rate found: ";
				return 0;
			}
		}
		else {
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
	}


//-------------------------------------------------------------------- *** table value functions

	function IsChargable() {
		if($this->IsRefundSituation()) {
			$this->Type = "Deductable";
			self::$is_chargable = false;
		}
		else {
			if($this->ID) {
				return $this->Type == 'Chargable';
			}
			else {
				$this->stat('is_chargable');
			}
		}
	}

	// note that this talks about AddedCharge, which can actually be zero while the table shows a value (inclusive case).

	function AddedCharge() {
		if($this->IsRefundSituation()) {
			return $this->Charge();
		}
		else {
			return $this->IsExclusive() ? $this->Charge() : 0;
		}
	}

	function getAmount() {
		if($this->IsExclusive() || $this->IsRefundSituation()) {
			if($this->ID) {
				return $this->getField("Amount");
			}
			else {
				return $this->LiveAmount();
			}
		}
		else {
			return 0;
		}
	}

	function TableAmount() {
		return $this->Charge();
	}

	function TableValue() {
		if(isset($_REQUEST["debug"])) {
			echo $this->debugMessage;
		}
		return "$".number_format(abs($this->Charge()), 2);
	}

//-------------------------------------------------------------------- *** title function

	protected function LiveName() {
		if($this->IsRefundSituation()) {
			$finalString = self::$refund_title;
			$countryCode = $this->LiveCountry();
		}
		else {
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
				if($name && $rate) {
					$finalString = $startString.$name.$endString;
				}
			}
			else {
				$finalString = self::$no_tax_description;
				$countryCode = $this->LiveCountry();
			}
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
		$deduct = 0;
		if($functionName = self::$order_item_function_for_tax_exclusive_portion)
			$items = ShoppingCart::get_items();
			//get index numbers for bonus products - this can only be done now once they have actually been added
			if($items) {
				foreach($items as $itemIndex => $item) {
					if(method_exists($item, $functionName)) {
						$deduct += $item->$functionName();
					}
				}
			}
		}
		return $order->SubTotal() + $order->ModifiersSubTotal(array("GSTTaxModifier")) - $deduct;
	}

	//this occurs when there is no country match and the rate is inclusive
	protected function IsRefundSituation() {
		if(!$this->LiveTaxObject()) {
			if($this->DefaultLiveTaxObject()) {
				if(!$this->LiveIsExclusive) {
					//IMPORTANT
					$this->debugMessage .= "<hr />IS REFUND SITUATION";
					self::$is_chargable = false;
					return true;
				}
			}
		}
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
		$this->Type = $this->IsChargable();

	}

	// ajax NEED TO OVERRIDE THE STANDARD ONE.
	function updateForAjax(array &$js) {
		$js[] = array('id' => $this->CartTotalID(), 'parameter' => 'innerHTML', 'value' => $this->Charge());
		$js[] = array('id' => $this->TableTotalID(), 'parameter' => 'innerHTML', 'value' => $this->TableValue());
		$js[] = array('id' => $this->TableTitleID(), 'parameter' => 'innerHTML', 'value' => $this->TableTitle());
	}

}













