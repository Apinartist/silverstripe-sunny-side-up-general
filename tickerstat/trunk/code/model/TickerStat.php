<?php


class TickerStat extends DataObject {

	/**
	 * number of items being retrieved to work out rate (e.g. 21)
	 * @var INT
	 */
	protected static $number_of_previous = 21;
		static function set_number_of_previous($i) {self::$number_of_previous = $i;}

	/**
	 *
	 * @var String
	 */
	protected static $json_import_url = "";
		static function set_json_import_url($s) {self::$json_import_url = $s;}


	/**
	 * used to store the calculated value: GrowthRateInItemsPerSecond
	 * @var Double | null
	 */
	private $perSecond = null;

	/**
	 * Standard SS variable.
	 */
	public static $db = array(
		"Code" => "Varchar",
		"RequestURL" => "Varchar",
		"Name" => "Varchar",
		"Number" => "Int"
	);

	/**
	 * Standard SS variable.
	 */
	public static $casting = array(
		"HasChange" => "Boolean",
		"MilliSecondsBetweenChange" => "Int",
		"ChangePerDay" => "Int",
		"Direction" => "Int"
	);

	/**
	 * Standard SS variable.
	 */
	public static $extensions = array(
		"Versioned('Stage')"
	);

	/**
	 * Standard SS variable.
	 */
	public static $indexes = array(
		"Code" => true
	);

	/**
	 * Standard SS variable.
	 */
	public static $searchable_fields = array(
		"Code" => "PartialMatchFilter"
	);

	/**
	 * Standard SS variable.
	 */
	public static $field_labels = array(
		"Code" => "Code for the statistic... has to be unique",
		"Name" => "Name of the statistic",
		"Number" => "Current number",
		"HasChange" => "Has Change within 24-hour period",
		"ChangePerDay" => "The increase per day",
		"MilliSecondsBetweenChange" => "Number of milli seconds (1/1000 of a second) between each change",
		"Direction" => "Does the number go up or down?"
	);

	/**
	 * Standard SS variable.
	 */
	public static $summary_fields = array(
		"Created" => "Created",
		"Name" => "Name",
		"Number" => "Number",
		"HasChange" => "HasChange",
		"ChangePerDay" => "ChangePerDay"
	);

	/**
	 * Standard SS variable.
	 */
	public static $default_sort = "LastEdited DESC, Code ASC";

	/**
	 * standard SS method
	 * Can the stat be deleted
	 * @param Member
	 * @return Boolean
	 */
	function canDelete($member = null) {
		return false;
	}

	/**
	 *
	 * Standard SS method
	 */
	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName("Versions");
		return $fields;
	}

	/**
	 * Casted Variable
	 * does it change at all per day?
	 * @return Boolean
	 */
	public function getHasChange(){
		return $this->getChangePerDay() ? 1 : 0;
	}

	/**
	 * Casted Variable
	 * does it change at all per day?
	 * @return Int
	 */
	public function getMilliSecondsBetweenChange(){
		$perSecond = $this->workOutGrowthRateInItemsPerSecond();
		if($perSecond) {
			$milliSecondsPerChange = (1/$perSecond) * 1000;
			return round($milliSecondsPerChange);
		}
		return 0;
	}

	/**
	 * Casted Variable
	 * number of changes per day as integer
	 * @return INT
	 */
	public function getChangePerDay(){
		$perSecond = $this->workOutGrowthRateInItemsPerSecond();
		$perDay = $perSecond * 60 * 60 * 24;
		if($this->debug) {
			echo "<br />Per Second".$perSecond;
			echo "<br />Per Day".$perDay;
		}
		return round($perDay)-0;
	}

	/**
	 * Casted Variable
	 * does the number go up or down?
	 * @return INT (1 or 0)
	 */
	public function getDirection(){
		$perSecond = $this->workOutGrowthRateInItemsPerSecond();
		return $perSecond >= 0 ? 1 : -1;
	}

	/**
	 * works out the number change per second
	 *
	 * @return DOUBLE
	 */
	protected function workOutGrowthRateInItemsPerSecond(){
		if($this->perSecond === null) {
			$lastEntries = DB::query("
				SELECT \"Number\", \"LastEdited\"
				FROM \"TickerStat_versions\"
				WHERE \"RecordID\" = ".$this->ID."
				ORDER BY \"LastEdited\" DESC
				LIMIT ".self::$number_of_previous);
			$maxTimeAsString = "";
			$minTimeAsString = "";
			$maxNumber = 0;
			$minNumber = 0;
			$count = 0;
			if($lastEntries) {
				foreach($lastEntries as $entryAsArray) {
					$count++;
					if(!$maxNumber) {
						$maxNumber = $entryAsArray["Number"];
					}
					$minNumber = $entryAsArray["Number"];
					if(!$maxTimeAsString) {
						$maxTimeAsString = $entryAsArray["LastEdited"];
					}
					$minTimeAsString = $entryAsArray["LastEdited"];
				}
			}
			$maxTimeTS = strtotime($maxTimeAsString);
			$minTimeTS = strtotime($minTimeAsString);
			$timeDifferenceInSeconds = $maxTimeTS - $minTimeTS;
			$numberDifference = $maxNumber - $minNumber;
			if($this->debug) {
				echo "<hr />";
				echo "<br />Min Time as String".$minTimeAsString;
				echo "<br />Max Time as String".$maxTimeAsString;
				echo "<br />Min Time as TS".$minTimeTS;
				echo "<br />Max Time as TS".$maxTimeTS;
				echo "<br />Min Number".$minNumber;
				echo "<br />Max Number".$maxNumber;
				echo "<br />Count".$count;
				echo "<br />TimeDifferenceInSeconds".$timeDifferenceInSeconds;
				echo "<br />NumberDifference".$numberDifference;
			}
			if($timeDifferenceInSeconds) {
				$this->perSecond = $numberDifference / $timeDifferenceInSeconds;
			}
			else {
				$this->perSecond = 0;
			}
			if($this->perSecond) {
				$currentTimeTS = strtotime("now");
				$differenceBetweenMaxTimeAndCurrentTimeAsTS = $currentTimeTS - $maxTimeTS;
				$this->Number += ($differenceBetweenMaxTimeAndCurrentTimeAsTS * $this->perSecond);
			}
		}
		return $this->perSecond;
	}

	/**
	 * set to true to see debug information
	 * @var Boolean
	 */
	private $debug = false;

	/**
	 * turn on debugging
	 */
	public function turnOnDebugging() {
		$this->debug = true;
	}

	/**
	 * imports data
	 * TO BE COMPLETED!
	 *
	 */
	public function import(){
		$oldStage = Versioned::current_stage();
		Versioned::reading_stage('Stage');
		$stats = DataObject::get("TickerStat");
		if($stats) {
			foreach($stats as $stat) {
				$url = $stat->RequestURL;
				if($this->checkIfExternalLinkWorks($url)) {
					$fileContents = file_get_contents($url);
					if(intval($fileContents)) {
						$stat->Number = intval($fileContents);
						$stat->write();
					}
				}
			}
		}
		Versioned::reading_stage($oldStage);
	}

	/**
	 * import stats from json
	 */
	public function jsonimport(){
		$oldStage = Versioned::current_stage();
		Versioned::reading_stage('Stage');
		$url = self::$json_import_url;
		if($this->checkIfExternalLinkWorks($url)) {
			$fileContents = file_get_contents($url);
			$json = @json_decode($fileContents, true);
			if($json) {
				if($this->debug) {echo "{";}
				foreach($json as $name => $number) {
					if($name && $number) {
						$tickerStat = DataObject::get_one("TickerStat", "\"Code\" = '$name'");
						if(!$tickerStat) {
							$tickerStat = new TickerStat();
						}
						$tickerStat->Number = intval($number);
						$tickerStat->Code = $name;
						if(!$tickerStat->Name) {
							$tickerStat->Name = $name;
						}
						if($this->debug) {echo "\"$name\": $number";}
						$tickerStat->write();
					}
					elseif($this->debug) {
						DB::alteration_message("error in number $number and name $name", "deleted");
					}
				}
				if($this->debug) {echo "}";}
			}
			elseif($this->debug) {
				DB::alteration_message("Error retrieving JSON ".print_r($json, true), "deleted");
			}
		}
		elseif($this->debug) {
			DB::alteration_message("Error retrieving URL: ".$url, "deleted");
		}
		Versioned::reading_stage($oldStage);
	}

	/**
	 * tells us whether it can open the URL
	 * @param String - URL
	 * @return Boolean
	 */
	protected function checkIfExternalLinkWorks($url) {
		// Version 4.x supported
		$handle   = curl_init($url);
		if (false === $handle){
			return false;
		}
		curl_setopt($handle, CURLOPT_HEADER, false);
		curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
		curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox
		curl_setopt($handle, CURLOPT_NOBODY, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		$connectable = curl_exec($handle);
		curl_close($handle);
		return $connectable;
	}


}

/**
 * Import and Export Stats
 *
 *
 */
class TickerStat_Controller extends Controller {



	function init(){
		parent::init();
	}

	/**
	 * returns dataobject as json
	 * @return String (JSON)
	 */
	function json(SS_HTTPRequest $request){
		$code = Convert::raw2sql($request->Param("ID"));
		$TickerStat = DataObject::get_one("TickerStat", "\"Code\" = '$code'");
		if($TickerStat) {
			$debug = $request->Param("OtherID");
			if($debug == "debug") {
				$TickerStat->turnOnDebugging();
			}
			$array = array(
				"Code" => $TickerStat->Code,
				"Name" => $TickerStat->Name,
				"Number" => $TickerStat->Number,
				"HasChange" => $TickerStat->HasChange,
				"MilliSecondsBetweenChange" => $TickerStat->MilliSecondsBetweenChange,
				"ChangePerDay" => $TickerStat->ChangePerDay
			);
			$json = Convert::array2json($array);
			$json = str_replace('\t', " ", $json);
			$json = str_replace('\r', " ", $json);
			$json = str_replace('\n', " ", $json);
			$json = preg_replace('/\s\s+/', ' ', $json);
			$json = str_replace("{", "\r\n{", $json);
			return $json;
		}
		$page = DataObject::get_one("ErrorPage", "ErrorCode = 404");
		if($page) {
			Director::redirect($page->Link());
		}
	}

}

/**
 * manage stats in CMS
 *
 *
 */
class TickerStat_ModelAdmin extends ModelAdmin {

	public static $managed_models = array("TickerStat");

	public static $url_segment = 'tickerstats';

	public static $menu_title = 'Ticker Stats';

	public $showImportForm = false;

}
