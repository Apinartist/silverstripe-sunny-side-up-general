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

class BlockCountriesModifier extends OrderModifier {

// --------------------------------------------------------------------*** static variables

	public static $db = array(
		"Error" => "Boolean",
		"CountryCode" => "Varchar(2)"
	);

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



//--------------------------------------------------------------------*** variables
	protected $messsage = '';
//--------------------------------------------------------------------*** display functions

	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		$this->checkCountry();
		return $this->Error;
	}

// -------------------------------------------------------------------- *** table values
	function LiveAmount() {
		$this->checkCountry();
		return 0;
	}

	function TableValue() {
		return 0;
	}


//--------------------------------------------------------------------*** table titles
	function LiveName() {
 		$this->messsage;
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
			$this->Error = 1;
			$this->CountryCode = $countryCode;
			$this->message = self::$sorry_message_start.Geoip::countryCode2name($countryCode).self::$sorry_message_end;
		}
	}

	function onBeforeWrite() {
		$this->checkCountry();
		if($this->Error) {
			die($this->message);
		}
		parent::onBeforeWrite();
	}



//-------------------------------------------------------------------- *** database functions
}



