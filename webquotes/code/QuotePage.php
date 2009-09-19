<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class QuotePage extends Page {

	static $icon = "webquote/images/treeicons/QuotePage";

	public static $db = array();

	public static $has_one = array(
		"TermsAndConditionsPage" => "SiteTree"
	);

	public static $has_many = array(
		"Quote" => "WebQuote"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Quotes",
			new HasManyComplexTableField(
				$controller = $this,
				$name = "Quote",
				$sourceClass = "WebQuote",
				$fieldList,
				$detailFormFields = null,
				$sourceFilter = "`WebQuote`.`ParentID` = ".$this->ID,
				$sourceSort = "",
				$sourceJoin = ""
			);
		);
		return $fields;
	}

	function canCreate() {
		return !DataObject::get("SiteTree", '`ClassName` = "QuotePage"');
	}

	function canDelete() {
		return false;
	}

	protected function getCurrencyRate($toCurrencyCode, $debug = false) {
	//$url = http://finance.yahoo.com/currency/convert?amt=1&from=NZD&to=USD&submit=Convert
		$url = 'http://download.finance.yahoo.com/d/quotes.csv?s=NZD'.$toCurrencyCode.'=X&f=sl1d1t1ba&e=.csv';
		if (($ch = @curl_init())) {
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, "$url");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$record = curl_exec($ch);
			if($debug) {echo "-- CURL:"; print_r($record);}
			curl_close($ch);
		}
		if(!$record) {
			$record = file_get_contents($url);
			if($debug) {echo "-- FILE_GET_CONTENTS:"; print_r($record);}
		}
		if ($record) {
			$currency_data = explode(',', $record);
			$rate = $currency_data[1];
			if(!$rate) {
				$rate = $currency_data[2];
			}
		}
		else {
			if($debug) {echo "-- could not retrieve data";}
			$rate = 0;
		}
		if($rate) {
			return $rate;
		}
		elseif($obj = DataObject::get_one("WebCurrencyConversion", "LCASE(`Code`) = ".strtolower($toCurrencyCode))) {
			return $obj->DefaultValue * $obj->Multiplier;
		}
		else {
			return 1;
		}
	}

}

class QuotePage_Controller extends Page_Controller {

}


	var $costPlan, $currencyPlan, $timePlan, $codeOption, $designOption, $templatePlan, $groupPlan, $d;
	var $personalised = 0;
	var $houseIntro;
	var $js = '';
	var $html = '';
	var $quote = '';
	var $countAll;


	function showHtml() {
		foreach($this->groupPlan as $key =>$value) {
			$this->html .= '
				<h3>'.$value[0].'</h3>';
			if($value[1]) {
				$this->html .= '
				<p>'.$value[1].' ('.$this->houseIntro.')</p>';
			}
			$this->html .= '
				<dl>';
			foreach($this->d as $keyItem=>$valueItem) {
				if($key == $valueItem[2]) {
					$this->html .= '<dt id="costingExp'.$keyItem.'">'.$valueItem[0].'</dt>';
					if($valueItem[5]) {
						$this->html .= '<dd> '.$valueItem[5].'</dd>';
					}
				}
			}
			$this->html .= '</dl>';
		}
		return $this->html;
	}

	function getClient () {
		$discountTable = $this->getClientDiscountTable();
		return $discountTable[0];
	}
	function getQuote($itemArray, $qtyArray, $costArray, $currencyCode) {
		$space = "\r\n\r\n ";
		$this->quote = '<table id="innerQuoted" summary="pricing">';
		$this->quote .= $space . '<tr>';
		$this->quote .= $space . '<th class="description" scope="col">description</th>';
		$this->quote .= $space . '<th class="price" scope="col">cost</th>';
		$this->quote .= $space . '</tr>';
		$totalCostMonthly = 0;
		$totalCostDevelopment = 0;
		foreach($itemArray as $key =>$itemId) {
			if($qtyArray[$key] > 1) {
				$quantity = intval($qtyArray[$key]).' x ';
			}
			else {
				$quantity = '';
			}
			$item = $this->d[$itemId];
			$this->quote .= $space . '<tr>';
			$this->quote .= $space . '<td class="description"><b>'.$quantity.$item[0].'</b> ';
			if($item[6] == 1) {
				$this->quote .= ' - cost per month ';
				$totalCostMonthly = $totalCostMonthly + ($costArray[$key] * (1/12));
				$cost = $costArray[$key]  * (1/12);
			}
			else {
				$totalCostDevelopment = $totalCostDevelopment + $costArray[$key];
				$cost = $costArray[$key];
			}
			if(trim($item[5])) {
				$this->quote .= '<i class="more">'.$item[5].'</i>';
			}
			$this->quote .= '</td>';
			$this->quote .= $space . '<td class="price">'.$currencyCode.number_format(round($cost, 2)).'</td>';
			$this->quote .= $space . '</tr>';
		}
		$this->quote .= $space . '<tr>';
		$this->quote .= $space . '<th class="description">total development cost</th>';
		$this->quote .= $space . '<th class="price">'.$currencyCode.number_format(round($totalCostDevelopment,2)).'</th>';
		$this->quote .= $space . '</tr>';
		$this->quote .= $space . '<tr>';
		$this->quote .= $space . '<th class="description">total monthly cost</th>';
		$this->quote .= $space . '<th class="price">'.$currencyCode.number_format(round($totalCostMonthly,2)).'</th>';
		$this->quote .= $space . '</tr>';
		$this->quote .= $space . '</table>';
		$this->quote .= $space . '<h2>Terms And Conditions</h2>';
		$this->quote .= $space . ''.$this->conditions().'';
		return $this->quote;
	}

	function showJS() {
		$variableArray = Array("costPlan", "currencyPlan", "timePlan", "codeOption", "designOption", "templatePlan", "groupPlan", "d");
		$discountTable = $this->getClientDiscountTable();
		if(isset($_GET["use"])) {
			$_GET["cost"] = 1;
		}
		if(count($discountTable)) {
			$this->personalised = 1;
		}
		$this->js =
			'var counter = 0;'
			.'var defaultItems = "";'
			.'var personalised = '.$this->personalised.';'
			.'var houseIntro = "'.$this->houseIntro.'";'
			.'var defaultCost = '.intval($_GET["cost"]+0).';'
			.'var defaultCurrency = '.($_GET["currency"]+0).';'
			.'var defaultTime = '.intval($_GET["time"]+0).';'
			.'var defaultCode = '.intval($_GET["code"]+0).';'
			.'var defaultDesign = '.intval($_GET["design"]+0).';';
		if(!isset($_GET["countAll"]) || $_GET["countAll"] == $this->countAll) {
			$this->js .=
				' defaultItems = "'.$_GET["items"].'";';
			if(isset($_GET["countAll"]) || $_GET["countAll"] == $this->countAll) {
				$this->js .= '
				jQuery(document).ready(
					function() {
						jQuery(".hSectionQuote").show();
						jQuery(".quoteSection").show();
					}
				);';
			}
		}
		else {
			$this->js .=
				'alert("Sorry, our prices have changed and your quote is no longer valid, please contact us directly for an updated quote.");';
		}

		$this->js .= 'var use = "'.$_GET["use"].'";';
		foreach($variableArray as $variable) {
			$this->js .= '
			var '.$variable.' = new Array(';
			$this->js .= $this->JSVariableDefinitionArray($this->$variable);
			$this->js .= ');
			';
		}
		if(count($discountTable)) {
			$this->js .=
					'costPlan[1][0] = "'.$discountTable[0].'"; costPlan[1][1] = '.$discountTable[1].';';
		}
		else {

		}
		return $this->js;
	}

	function getClientDiscountTable() {
	//http://www.randpass.com/cgi-bin/advanced
		static $final;
		if(count($final)) {
			return $final;
		}
		$client = Array ();
		$handle = @fopen("c/rekening.txt", "r");
		$i = 0;
		if ($handle) {
			while (!feof($handle)) {
				$client = explode(";", fgets($handle, 4096));
				if((trim($client[0])) == $_GET["use"]) {
					$client[1] = trim($client[1]);
					$client[2] = floatval($client[2] - 0);
					$final = Array($client[1], $client[2]);
					return $final;
				}
				$i++;
			}
			fclose($handle);
		}
		return null;
	}

	function JSVariableDefinitionArray($array, $js = '') {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				if($key) {
					$this->js .= ', ';
				}
				$this->js .= ' new Array( ';
				$this->js .= $this->JSVariableDefinitionArray($value, $js);
				$this->js .= ' )';
			}
			else {
				$floatvalValue = floatval($value);
				if($floatvalValue === $value || intval($value) === $value) {
					$x = '';
				}
				else {
					$x = '"';
					$value = str_replace("/", "\/", $value);
				}
				if($key) {
					$this->js .= ', ';
				}
				$this->js .= $x.$value.$x;
			}
		}
		return $js;
	}
