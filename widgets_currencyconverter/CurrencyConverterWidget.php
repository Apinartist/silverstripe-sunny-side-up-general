<?php

/*
 set default using Javascript
*/

class CurrencyConverterWidget extends Widget {
 static $title = 'Currency Converter';
 static $cmsTitle = 'Currency Converter';
 static $description = 'Allows users to convert any amount FROM one TO another currency.';

 static $db = array(
	"DefaultFromCurrencyCode" => "Varchar(3)",
	"DefaultToCurrencyCode" => "Varchar(3)",
	"DefaultAmount" => "Currency"
 );
 static $defaults = array(
	"DefaultFromCurrency" => "EUR",
	"DefaultToCurrency" => "USD",
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

 var $fromCurrencyCode = '';
 var $toCurrencyCode = '';
 var $amount = 0;
 var $rate = 0;
 var $debug = 0;

 public function setFromCurrencyCode($fromCurrencyCode = '') {
	$this->fromCurrencyCode = ($fromCurrencyCode);
	Session::set("CurrencyConverter.FromCurrencyCode", $fromCurrencyCode);
 }
 public function setToCurrencyCode($toCurrencyCode = '') {
	$this->toCurrencyCode = ($toCurrencyCode);
	Session::set("CurrencyConverter.ToCurrencyCode", $toCurrencyCode);
 }
 public function setAmount($amount = 0) {
	$this->amount = $amount;
	Session::set("CurrencyConverter.Amount", $amount);
 }

 public function getExchangedAmount() {
	$this->getValues();
	if($this->debug) {echo "-- from ".$this->fromCurrencyCode;}
	if($this->debug) {echo "-- to ".$this->toCurrencyCode;}
	if($this->debug) {echo "-- amount ".$this->amount;}
	//cleanse
	$this->toCurrencyCode = strtoupper($this->toCurrencyCode);
	$this->fromCurrencyCode = strtoupper($this->fromCurrencyCode);
	$this->amount = floatval($this->amount);
	return $this->toCurrencyCode.round(floatval($this->getRate() * $this->amount), 2);
 }

 public function getAmount() {
	$this->getValues();
	return $this->amount;
 }

 public function CurrencyConverter() {
	$this->getValues();
	$convertedAmount = $this->getExchangedAmount();
	$output = new DataObjectSet();
	$output->push(
	 new ArrayData(
		array(
		 "fromCurrencyCode" =>  $this->fromCurrencyCode,
		 "toCurrencyCode" => $this->toCurrencyCode,
		 "amount" => $this->amount,
		 "rate" => floatval($this->rate+0),
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
	 $from = ($key == $this->fromCurrencyCode);
	 $to = ($key == $this->toCurrencyCode);
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

 public function getCMSFields() {
	return new FieldSet(
	 new TextField("DefaultFromCurrencyCode", _t('CurrencyConverterWidget.DefaultFromCurrencyCode', "Default From Currency Code")),
	 new TextField("DefaultToCurrencyCode", _t('CurrencyConverterWidget.DefaultToCurrencyCode', "Default To Currency Code")),
	 new CurrencyField("DefaultAmount", _t('CurrencyConverterWidget.DefaultAmount', "Default Amount to be Converted"))
	);
 }

 private function getValues() {
	if(isset($_GET["f"])  ) {
	 $this->fromCurrencyCode = strtolower(substr($_GET["f"], 0, 3));
	 $this->setFromCurrencyCode($this->fromCurrencyCode);
	}
	if( isset($_GET["t"])  ) {
	 $this->toCurrencyCode = strtolower(substr($_GET["t"], 0, 3));
	 $this->setToCurrencyCode($this->toCurrencyCode);
	}
	if(isset($_GET["a"]) ) {
	 $this->amount = floatval($_GET["a"]);
	 $this->setAmount($this->amount);
	}
	if(!$this->fromCurrencyCode) {
	 if(!$this->fromCurrencyCode = Session::get("CurrencyConverter.FromCurrencyCode")) {
		$this->fromCurrencyCode = $this->DefaultFromCurrencyCode;
	 }
	}
	if(!$this->toCurrencyCode) {
	 if(!$this->toCurrencyCode = Session::get("CurrencyConverter.ToCurrencyCode")) {
		$this->toCurrencyCode = $this->DefaultToCurrencyCode;
	 }
	}
	if(!$this->amount) {
	 if(!$this->amount = Session::get("CurrencyConverter.Amount")) {
		$this->amount = $this->DefaultAmount;
	 }
	}
 }

 private function getRate() {
	//$url = http://finance.yahoo.com/currency/convert?amt=1&from=NZD&to=USD&submit=Convert
	if($this->fromCurrencyCode && $this->toCurrencyCode) {
	 $url = 'http://download.finance.yahoo.com/d/quotes.csv?s='.$this->fromCurrencyCode.$this->toCurrencyCode.'=X&f=sl1d1t1ba&e=.csv';
	 if (($ch = @curl_init())) {
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, "$url");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$record = curl_exec($ch);
		if($this->debug) {echo "-- CURL:"; print_r($record);}
		curl_close($ch);
	 }
	 if(!$record) {
		$record = file_get_contents($url);
		if($this->debug) {echo "-- FILE_GET_CONTENTS:"; print_r($record);}
	 }
	 if ($record) {
		$currency_data = explode(',', $record);
		$this->rate = $currency_data[1];
		if(!$this->rate) {
		 $this->rate = $currency_data[2];
		}
	 }
	 else {
		if($this->debug) {echo "-- could not retrieve data";}
	 }
	 if($this->rate) {
		return $this->rate;
	 }
	}
	if($this->debug) {echo "-- could not find rate";}
	return 0;
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
