<?php


/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description:  allows you to check rules like this
 * if(Modifier A::variable = ... set Modifier B::variable to ...)
 * variables can also be function
 * it checks for the function first and then does the variable
 */

class ModifierRulesModifier extends OrderModifier {

// --------------------------------------------------------------------*** static variables

	public static $db = array(
	);

	protected static $rule_array = array();

	protected static $must_include_country_codes = array();
		static function set_must_include_country_codes(array $array) {self::$must_include_country_codes = $array;}

	protected static $must_not_country_codes = array();
		static function set_must_not_country_codes(array $array) {self::$must_not_country_codes = $array;}

	protected static $sorry_message_start = "Sorry, but sales to."
		static function set_sorry_message_start($string) {self::$sorry_message_start = $string;}

	protected static $sorry_message_end = "are not available."
		static function set_sorry_message_start($string) {self::$sorry_message_start = $string;}

// --------------------------------------------------------------------*** static functions


	static function show_form() {
		false;
	}

	static function get_form($controller) {
		return false;
	}



//--------------------------------------------------------------------*** display functions
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return false;
	}

// -------------------------------------------------------------------- *** table values
	function LiveAmount() {
		$this->checkCountry();
		return 9999999999;
	}

	function TableValue() {
		return "";
	}


//--------------------------------------------------------------------*** table titles
	function LiveName() {
		return "";
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


//--------------------------------------------------------------------*** calculations
	protected function checkCountry() {
		$stop = false;
		$countryCode = EcommerceRole::findCountry();
		if($countryCode) {
			if(count(self::$must_include_country_codes)) {
				if(!in_array($countryCode, self::$must_include_country_codes)) {
					$stop = true;
				}
			}
			if(count(self::$must_not_include_country_codes)) {
				if(in_array($countryCode, self::$must_not_include_country_codes)) {
					$stop = true;
				}
			}
		}
		if($stop) {

		}
	}




//-------------------------------------------------------------------- *** database functions
}



