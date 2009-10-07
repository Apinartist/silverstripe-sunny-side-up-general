<?php

class TrainingHolder extends Page {

	static $icon = "mysite/images/treeicons/TrainingHolder";

	//parents and children in sitetree
	static $allowed_children = array("TrainingPage"); //can also be "none";
	static $default_child = "TrainingPage";

	public function canCreate($member = null) {
		return DataObject::get_one('TrainingHolder') == null;
	}

	public function canDelete($member = null) {
		return false;
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}
}

class TrainingHolder_Controller extends Page_Controller {

	function MonthlyCourses() {
		$results = new DataObjectSet();

		$stage = Versioned::current_stage();
		$suffix = (!$stage || $stage == 'Stage') ? "" : "_$stage";

		$sqlResults = DB::query("
			SELECT DISTINCT MONTH(`Date`) AS `Month`, YEAR(`Date`) AS `Year`
			FROM `SiteTree$suffix` NATURAL JOIN `TrainingPage$suffix`
			WHERE `ParentID` = ".$this->ID." AND `Date` > CONVERT_TZ(now(),'+00:00','+13:00')
			ORDER BY `Year` DESC, `Month` ASC;"
		);

		if($sqlResults) foreach($sqlResults as $sqlResult) {
			$month = (isset($sqlResult['Month'])) ? (int) $sqlResult['Month'] : 1;
			$year = ($sqlResult['Year']) ? (int) $sqlResult['Year'] : date('Y');

			$date = DBField::create('Date', array(
				'Day' => 1,
				'Month' => $month,
				'Year' => $year
			));


			$results->push(new ArrayData(array(
				'Date' => $date,
				'Courses' => DataObject::get("TrainingPage", "`ShowInMenus` = 1 AND MONTH(`TrainingPage`.Date) = $month AND YEAR(`TrainingPage`.Date) = $year")
			)));
		}
		return $results;
	}

}
