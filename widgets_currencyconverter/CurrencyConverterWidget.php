<?php

/*
				$page->defaultFromCurrencyCode = "NZD";
				$page->Defaultto_currency_code = "EUR";
				$page->DefaultAmount = "100";
*/

class CurrencyConverterWidget extends Widget {

	static $title = 'Currency Converter';

	static $cmsTitle = 'Currency Converter';

	static $description = 'Allows users to convert any amount FROM one TO another currency.';

	static $db = array(
		"DefaultFromCurrency" => "Varchar(3)",
		"DefaultToCurrency" => "Varchar(3)",
		"DefaultAmount" => "Currency"
	);

	static $defaults = array(
		"DefaultFromCurrency" => "NZD",
		"DefaultToCurrency" => "EUR",
		"DefaultAmount" => "1"
	);

	protected static $currency_list = array(
		"-0-" => "-- Main Currencies: --",
		"eur" => "euro",
		"usd" => "united states dollars",
		"gbp" => "united kingdom pounds",
		"cad" => "canada dollars",
		"aud" => "australia dollars",
		"jpy" => "japan yen",
		"inr" => "india rupees",
		"chf" => "switzerland francs",
		"zar" => "south africa rand",
		"-2-" => "-- top 85 currencies --",
		"afa" => "afghanistan afghanis",
		"all" => "albania leke",
		"dzd" => "algeria dinars",
		"usd" => "america (united states) dollars",
		"ars" => "argentina pesos",
		"aud" => "australia dollars",
		"ats" => "austria schillings*",
		"bsd" => "bahamas dollars",
		"bhd" => "bahrain dinars",
		"bdt" => "bangladesh taka",
		"bbd" => "barbados dollars",
		"bef" => "belgium francs*",
		"bmd" => "bermuda dollars",
		"brl" => "brazil reais",
		"bgn" => "bulgaria leva",
		"cad" => "canada dollars",
		"xof" => "cfa bceao francs",
		"xaf" => "cfa beac francs",
		"clp" => "chile pesos",
		"cny" => "china yuan renminbi",
		"cop" => "colombia pesos",
		"crc" => "costa rica colones",
		"hrk" => "croatia kuna",
		"cyp" => "cyprus pounds",
		"czk" => "czech republic koruny",
		"dkk" => "denmark kroner",
		"dem" => "deutsche (germany) marks*",
		"dop" => "dominican republic pesos",
		"nlg" => "dutch (netherlands) guilders*",
		"xcd" => "eastern caribbean dollars",
		"egp" => "egypt pounds",
		"eek" => "estonia krooni",
		"eur" => "euro",
		"fjd" => "fiji dollars",
		"fim" => "finland markkaa*",
		"frf" => "france francs*",
		"dem" => "germany deutsche marks*",
		"xau" => "gold ounces",
		"grd" => "greece drachmae*",
		"nlg" => "holland (netherlands) guilders*",
		"hkd" => "hong kong dollars",
		"huf" => "hungary forint",
		"isk" => "iceland kronur",
		"xdr" => "imf special drawing right",
		"inr" => "india rupees",
		"idr" => "indonesia rupiahs",
		"irr" => "iran rials",
		"iqd" => "iraq dinars",
		"iep" => "ireland pounds*",
		"ils" => "israel new shekels",
		"itl" => "italy lire*",
		"jmd" => "jamaica dollars",
		"jpy" => "japan yen",
		"jod" => "jordan dinars",
		"kes" => "kenya shillings",
		"krw" => "korea (south) won",
		"kwd" => "kuwait dinars",
		"lbp" => "lebanon pounds",
		"luf" => "luxembourg francs*",
		"myr" => "malaysia ringgits",
		"mtl" => "malta liri",
		"mur" => "mauritius rupees",
		"mxn" => "mexico pesos",
		"mad" => "morocco dirhams",
		"nlg" => "netherlands guilders*",
		"nzd" => "new zealand dollars",
		"nok" => "norway kroner",
		"omr" => "oman rials",
		"pkr" => "pakistan rupees",
		"xpd" => "palladium ounces",
		"pen" => "peru nuevos soles",
		"php" => "philippines pesos",
		"xpt" => "platinum ounces",
		"pln" => "poland zlotych",
		"pte" => "portugal escudos*",
		"qar" => "qatar riyals",
		"rol" => "romania lei",
		"rub" => "russia rubles",
		"sar" => "saudi arabia riyals",
		"xag" => "silver ounces",
		"sgd" => "singapore dollars",
		"skk" => "slovakia koruny",
		"sit" => "slovenia tolars",
		"zar" => "south africa rand",
		"krw" => "south korea won",
		"esp" => "spain pesetas*",
		"xdr" => "special drawing rights (imf)",
		"lkr" => "sri lanka rupees",
		"sdd" => "sudan dinars",
		"sek" => "sweden kronor",
		"chf" => "switzerland francs",
		"twd" => "taiwan new dollars",
		"thb" => "thailand baht",
		"ttd" => "trinidad and tobago dollars",
		"tnd" => "tunisia dinars",
		"try" => "turkey new lira",
		"trl" => "turkey lira*",
		"aed" => "united arab emirates dirhams",
		"gbp" => "united kingdom pounds",
		"usd" => "united states dollars",
		"veb" => "venezuela bolivares",
		"vnd" => "vietnam dong",
		"zmk" => "zambia kwacha"
	);

	protected static $rates = array();

	protected static $from_currency_code = '';

	protected static $to_currency_code = '';

	private static $debug = false;

	protected $amount = 0;


	// set once....

	static function set_debug_mode($trueOrFalse) {
		self::$debug = $trueOrFalse;
	}

	static function set_from_currency_code($v) {
		if(self::currency_exists($v)) {
			self::$from_currency_code = $v;
			Session::set("CurrencyConverter.from_currency_code", $v);
		}
	}

	static function set_to_currency_code($v) {
		if(self::currency_exists($v)) {
			self::$to_currency_code = $v;
			Session::set("CurrencyConverter.to_currency_code", $v);
		}
	}

	static function get_from_currency_code() {
		return self::$from_currency_code;
	}

	static function get_to_currency_code() {
		return self::$to_currency_code;
	}

	static function from_equals_to() {
		self::retrieveDefaults();
		return (self::$to_currency_code == self::$from_currency_code);
	}

	static function has_from_and_to_currencies() {
		self::retrieveDefaults();
		if(self::$to_currency_code && self::$from_currency_code) {
			return true;
		}
	}


	static function get_rate() {
		//$url = http://finance.yahoo.com/currency/convert?amt=1&from=NZD&to=USD&submit=Convert
		if(self::has_from_and_to_currencies()) {
			if(isset(self::$rates[self::$from_currency_code.".".self::$to_currency_code]) && self::$rates[self::$from_currency_code.".".self::$to_currency_code] > 0) {
				return self::$rates[self::$from_currency_code.".".self::$to_currency_code]+0;
			}
			else {
				$url = 'http://download.finance.yahoo.com/d/quotes.csv?s='.self::$from_currency_code.self::$to_currency_code.'=X&f=sl1d1t1ba&e=.csv';
				if (($ch = @curl_init())) {
				$timeout = 5; // set to zero for no timeout
				curl_setopt ($ch, CURLOPT_URL, "$url");
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$record = curl_exec($ch);
				if(self::$debug) {echo "-- CURL:"; print_r($record);}
				curl_close($ch);
				}
				if(!$record) {
					$record = file_get_contents($url);
					//if(self::$debug) {echo "-- FILE_GET_CONTENTS:"; print_r($record);}
				}
				if ($record) {
					$currency_data = explode(',', $record);
					self::$rates[self::$from_currency_code.".".self::$to_currency_code] = $currency_data[1];
					if(!isset(self::$rates[self::$from_currency_code.".".self::$to_currency_code]) || !self::$rates[self::$from_currency_code.".".self::$to_currency_code]) {
						self::$rates[self::$from_currency_code.".".self::$to_currency_code] = $currency_data[2];
					}
				}
				else {
					if(self::$debug) {echo "-- could not retrieve data";}
				}
			}
		}
		else {
			if(self::$debug) {echo "-- could not find from and to values!";}
		}
		if(isset(self::$rates[self::$from_currency_code.".".self::$to_currency_code])) {
			return self::$rates[self::$from_currency_code.".".self::$to_currency_code]+0;
		}
		else {
			return 0;
		}
	}

	static function get_exchanged_value($amount = 0) {
		self::retrieveDefaults();
		if(self::$debug) {echo "-- from ".self::$from_currency_code;}
		if(self::$debug) {echo "-- to ".self::$to_currency_code;}
		if(self::$debug) {echo "-- amount ".$amount;}
		$rate = self::get_rate();
		if(self::$debug) {echo "-- rate ".$rate;}
		return strtoupper(self::$to_currency_code).' '.round(floatval($rate * $amount), 2);
	}

	private static function retrieveDefaults() {
		if(!self::$from_currency_code) {
			if(self::$from_currency_code = Session::get("CurrencyConverter.from_currency_code")) {
			}
			else {
				self::$from_currency_code = self::$defaults["DefaultFromCurrency"];
			}
		}
		if(!self::$to_currency_code) {
			if(self::$to_currency_code = Session::get("CurrencyConverter.to_currency_code")) {
			}
			else {
				self::$to_currency_code = self::$defaults["DefaultToCurrency"];
			}
		}
	}


	private static function currency_exists($v) {
		$outcome = array_key_exists($v, self::$currency_list);
		if(!$outcome && self::$debug) {
			die( "$v currency could not be found!");
		}
		return $outcome;
	}

	//CMS ...

	public function getCMSFields() {
		return new FieldSet(
			new TextField("defaultFromCurrencyCode", _t('CurrencyConverterWidget.defaultFromCurrencyCode', "Default From Currency Code")),
			new TextField("Defaultto_currency_code", _t('CurrencyConverterWidget.Defaultto_currency_code', "Default To Currency Code")),
			new CurrencyField("DefaultAmount", _t('CurrencyConverterWidget.DefaultAmount', "Default Amount to be Converted"))
		);
	}

	//set for every transaction....

	public function setAmount($amount = 0) {
		$this->amount = floatval($amount);
		Session::set("CurrencyConverter.Amount", $amount);
	}

	// get for every transaction ...

	public function getExchangedAmount($amount = 0) {
		$this->getValues();
		if($amount) {
			$this->amount = $amount;
		}
		$this->amount = floatval($this->amount);
		return self::get_exchanged_value($this->amount);
	}

	// for templates ...

	public function CurrencyConverter() {
		$this->getValues();
		$convertedAmount = $this->getExchangedAmount();
		$output = new DataObjectSet();
		$output->push(
			new ArrayData(
			array(
				"from_currency_code" =>  self::$from_currency_code,
				"to_currency_code" => self::$to_currency_code,
				"amount" => $this->amount,
				"rate" => floatval(self::$rates[self::$from_currency_code.".".self::$to_currency_code]+0),
				"convertedAmount" => $convertedAmount
			)
			)
		);
		return $output;
	}

	public function Currencies() {
		$this->getValues();
		$currencies = new DataObjectSet;
		foreach(self::$currency_list as $key => $value) {
			$from = ($key == self::$from_currency_code);
			$to = ($key == self::$to_currency_code);
			$item = new ArrayData(
			Array(
				"code" => $key,
				"name" => $value,
				"currentFrom" => $from,
				"currentTo" => $to
			)
			);
			$currencies->push($item);
		}
		return $currencies;
	}


	private function retrieveGetValues() {
		if(isset($_GET["f"])  ) {
			self::set_from_currency_code(strtolower(substr($_GET["f"], 0, 3)));
		}
		if( isset($_GET["t"])  ) {
			self::set_to_currency_code(strtolower(substr($_GET["t"], 0, 3)));
		}
		if(isset($_GET["a"]) ) {
			$this->amount = floatval($_GET["a"]);
			$this->setAmount($this->amount);
		}
	}



}


class CurrencyConverterWidget_Controller extends ContentController {
	function __construct($dataRecord = Null) {
		parent::__construct($dataRecord);
	}

	function getRate() {
		$CurrencyConverterWidgetObject = new CurrencyConverterWidget();
		//$CurrencyConverterWidgetObject->debug = true;
		echo $CurrencyConverterWidgetObject->getExchangedAmount();
	}
}
