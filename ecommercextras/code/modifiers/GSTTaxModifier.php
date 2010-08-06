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
		'TableValue' => 'Currency',
		'Name' => 'Text',
		'TaxType' => "Enum('Exclusive, Inclusive','Exclusive')",
		'DebugString' => 'HTMLText'
	);

	static $casting = array(
		'TaxableAmount' => 'Currency'
	);


	//-------------------------------------------------------------------- *** model admin

	public static $searchable_fields = array(
		"OrderID",
		'Country',
		'Rate',
		'Amount',
		'TableValue',
		'DebugString' => "PartialMatchFilter"
	);
	public static $summary_fields = array(
		"Created",
		"OrderID",
		'Country',
		'Rate',
		'Amount',
		'TableValue'
	);
	public static $singular_name = "Tax Charge";
	public static $plural_name = "Tax Charges";
	//CRUD settings
	public function canCreate() {return false;}
	public function canView() {return true;}
	public function canEdit() {return false;}
	public function canDelete() {return false;}
	//defaults
	public static $default_sort = "Created DESC";


	protected static $default_country_code = "NZ";
		static function set_default_country_code($v) {self::$default_country_code = $v;}

	protected static $fixed_country_code = "";
		static function set_fixed_country_code($v) {self::$fixed_country_code = $v;}

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

	protected static $current_country_code = "";

	protected static $tax_objects = null;

	protected static $live_rate = 0;

	protected static $is_chargable = true;

//-------------------------------------------------------------------- *** static functions

	static function override_country($countryCode) {
		user_error("GSTTaxModifier::override_country is no longer in use, please use GSTTaxModifier::set_fixed_country_code", E_USER_NOTICE);
		self::set_fixed_country_code($countryCode);
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
		if($obj = $this->LivetaxObjects()) {
			$obj->InclusiveOrExclusive;
		}
		else {
			return false;
		}
	}

//--------------------------------------------------------------------*** other attribute functions: country

	protected function LiveCountry() {
		if(!self::$current_country_code) {
			$fixedCode = Session::get("GSTTaxModifier_CountryCode");
			if($fixedCode || self::$fixed_country_code) {
				if(!$fixedCode) {
					$fixedCode = self::$fixed_country_code;
					Session::set("GSTTaxModifier_CountryCode", self::$fixed_country_code);
				}
				Session::set("GSTTaxModifier_CountryCode", self::$fixed_country_code);
				$this->debugMessage .= "<hr />OVERRIDING COUNTRY CODE TO: ".$fixedCode;
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
		}
		return self::$current_country_code;
	}

	function LivetaxObjects() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if(!self::$tax_objects) {
			if($countryCode = $this->LiveCountry()) {
				$this->debugMessage .= "<hr />There is a current live country: ".$countryCode;
				$objects = DataObject::get("GSTTaxModifierOptions", "{$bt}CountryCode{$bt} = '".$countryCode."'");
				if($objects) {
					$this->debugMessage .= "<hr />There are tax objects available for ".$countryCode;
					self::$tax_objects = $objects;
				}
				else {
					$this->debugMessage .= "<hr />there are no tax objects available for ".$countryCode;
				}
			}
			else {
				$this->debugMessage .= "<hr />There are no current live tax objects (no country specified), using default country instead";
				self::$tax_objects = $this->DefaultLivetaxObjects();
			}
		}
		return self::$tax_objects;
	}

	function DefaultLivetaxObjects() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if(!self::$tax_objects) {
			$defaultCountryCode = GSTTaxModifier::$default_country_code;
			if($defaultCountryCode) {
				$this->debugMessage .= "<hr />There are current live DEFAULT country code: ".$defaultCountryCode;
				$objects = DataObject::get("GSTTaxModifierOptions", "{$bt}CountryCode{$bt} = '".$defaultCountryCode."'");
				if($objects){
					$this->debugMessage .= "<hr />there are DEFAULT tax objects available for ".$defaultCountryCode;
					self::$tax_objects = $objects;
				}
				else {
					$this->debugMessage .= "<hr />there are no DEFAULT tax object available for ".$defaultCountryCode;
				}
			}
			else {
				$this->debugMessage .= "<hr />There are no current live DEFAULT tax object";
			}
		}
		return self::$tax_objects;
	}

//--------------------------------------------------------------------*** rates functions

	protected function LiveRate() {
		if(!self::$live_rate) {
			if($this->IsRefundSituation()) {
				//need to use default here as refund is always based on default country!
				$taxObjects = $this->DefaultLivetaxObjects();
				if($sumRate = $this->workOutSumRate($taxObjects)) {
					$this->debugMessage .= "<hr />using DEFAULT (REFUND) rate: ".$sumRate;
					self::$live_rate = $sumRate;
				}
				else {
					$this->debugMessage .= "<hr />no DEFAULT (REFUND) rate found, using: 0 ";
					self::$live_rate = 0;
				}
			}
			else {
				$taxObjects = $this->LivetaxObjects();
				if($sumRate = $this->workOutSumRate($taxObjects)) {
					$this->debugMessage .= "<hr />using rate: ".$sumRate;
					self::$live_rate = $sumRate;
				}
				else {
					$this->debugMessage .= "<hr />no rate found, using: 0";
					self::$live_rate = 0;
				}
			}
		}
		return self::$live_rate;
	}

	protected function workOutSumRate($taxObjects) {
		$sumRate = 0;
		if($taxObjects) {
			foreach($taxObjects as $obj) {
				$this->debugMessage .= "<hr />found a rate of ".$obj->Rate;
				$sumRate += floatval($obj->Rate);
			}
		}
		else {
			$this->debugMessage .= "<hr />could not find a rate";
		}
		return $sumRate;
	}

//-------------------------------------------------------------------- *** table value functions

	function IsChargable() {
		if($this->IsRefundSituation()) {
			$this->Type = "Deductable";
			self::$is_chargable = false;
		}
		else {
			if($this->ID) {
				$this->Type == 'Chargable';
				self::$is_chargable = $this->Type;
			}
			else {
				self::$is_chargable = $this->stat('is_chargable');
			}
		}
		return self::$is_chargable;
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

	function Charge() {
		if($this->ID) {
			$this->TableValue;
		}
		else {
			$rate = ($this->IsExclusive() ? $this->Rate() : (1 - (1 / (1 + $this->Rate()))));
			return $this->TaxableAmount() * $rate;
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
		if($this->ID) {
			$value = $this->TableValue;
		}
		else {
			$value = $this->Charge();
		}
		if(!$value) {
			$value = 0;
		}
		return $value;
	}

//-------------------------------------------------------------------- *** title function

	protected function LiveName() {
		$finalString = "tax could not be determined";
		if($this->IsRefundSituation()) {
			$finalString = self::$refund_title;
			$countryCode = $this->LiveCountry();
		}
		else {
			$start = '';
			$name = '';
			$end = '';
			$taxObjects = $this->LivetaxObjects();
			if($taxObjects) {
				$objectArray = array();
				foreach($taxObjects as $object) {
					$objectArray[] = $object->Name;
				}
				if(count($objectArray)) {
					$name = implode(", ", $objectArray);
				}
				if($rate = $this->LiveRate()) {
					$startString = number_format($this->LiveRate() * 100, 2) . '% ';
				}
				if( $this->IsExclusive()) {
					$endString = self::$exclusive_explanation;
				}
				else {
					$endString = self::$inclusive_explanation;
				}
				$countryCode = $this->LiveCountry();
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
		if($functionName = self::$order_item_function_for_tax_exclusive_portion) {
			$items = ShoppingCart::get_items();
			if($items) {
				foreach($items as $itemIndex => $item) {
					if(method_exists($item, $functionName)) {
						$deduct += $item->$functionName();
					}
				}
			}
		}
		$subTotal = $order->SubTotal();
		$modifierTotal = $order->ModifiersSubTotal(array("GSTTaxModifier"));
		$this->debugMessage .= "<hr />using sub-total: ".$subTotal;
		$this->debugMessage .= "<hr />using modifer-total: ".$modifierTotal;
		$this->debugMessage .= "<hr />using non-taxable portion: ".$deduct;
		return  $subTotal + $modifierTotal - $deduct;
	}

	//this occurs when there is no country match and the rate is inclusive
	protected function IsRefundSituation() {
		if(!$this->LivetaxObjects()) {
			if($this->DefaultLivetaxObjects()) {
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
		$this->DebugString .= "<hr />recording a table value of : ".$this->TableValue;
	}

	// ajax NEED TO OVERRIDE THE STANDARD ONE.
	function updateForAjax(array &$js) {
		$js[] = array('id' => $this->CartTotalID(), 'parameter' => 'innerHTML', 'value' => $this->Charge());
		$js[] = array('id' => $this->TableTotalID(), 'parameter' => 'innerHTML', 'value' => $this->TableValue());
		$js[] = array('id' => $this->TableTitleID(), 'parameter' => 'innerHTML', 'value' => $this->TableTitle());
	}

	function DebugMessage () {
		if(Director::isDev()) {return $this->debugMessage;}
	}

}













