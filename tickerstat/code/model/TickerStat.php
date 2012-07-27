<?php


class TickerStat extends DataObject {

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
		"SecondsBetweenChange" => "Number of seconds between each change",
		"Direction" => "Does the number go up or down?"
	);

	/**
	 * Standard SS variable.
	 */
	public static $summary_fields = array(
		"Created" => "Created",
		"Name" => "Name",
		"HasChange" => "HasChange",
		"ChangePerDay" => "ChangePerDay"
	);

	/**
	 * Standard SS variable.
	 */
	public static $default_sort = "LastEdited DESC, Code ASC";

	function canDelete($member) {
		return false;
	}

	/**
	 *
	 * Standard SS method
	 */
	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName("Version");
		return $fields;
	}

	/**
	 * Casted Variable
	 * does it change at all per day?
	 * @return Boolean
	 */
	public function getHasChange(){
		return $this->getChangePerDay() ? true : false;
	}

	/**
	 * Casted Variable
	 * does it change at all per day?
	 * @return Int
	 */
	public function getMilliSecondsBetweenChange(){
		$perSecond = $this->workOutGrowthRateInItemsPerSecond();
		$perChange = (1/$perSecond) * 1000;
		return $perChange;
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
		return round($perDay);
	}

	/**
	 * Casted Variable
	 * does the number go up or down?
	 * @return INT (1 or 0)
	 */
	public function getDirection(){
		$perSecond = $this->workOutGrowthRateInItemsPerSecond();
		return $perSecond > 0 ? 1 : -1;
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

}

/**
 * Import and Export Stats
 *
 *
 */
class TickerStat_Controller extends Controller {

	protected static $number_of_records = 21;

	function init(){
		parent::init();
	}

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

	/**
	 * imports data
	 * TO BE COMPLETED!
	 *
	 */
	function import(){
		$stats = DataObject::get("TickerStat");
		foreach($stats as $stat) {
			$url = $stat->RequestURL;
			if($this->checkIfExternalLinkWorks($url)) {
				$fileContents = file_get_contents(self::$request_url);
				if(intval($fileContents)) {
					$stat->Number = intval($fileContents);
					$stat->write();
				}
			}
		}
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
