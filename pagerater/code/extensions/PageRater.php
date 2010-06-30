<?php

/**
 *@author nicolaas [at] sunnysideup up .co .nz
 *
 *
 **/

class PageRater extends DataObjectDecorator {

	protected static $round_rating = true;
		static function set_round_rating($v) {self::$round_rating = $v;}
		static function get_round_rating() {return self::$round_rating;}

	protected static $number_of_default_records_to_be_added = true;
		static function set_number_of_default_records_to_be_added($v) {self::$number_of_default_records_to_be_added = $v;}
		static function get_number_of_default_records_to_be_added() {return self::$number_of_default_records_to_be_added;}

	function PageRatingResults() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$doSet = new DataObjectSet();
    $sqlQuery = new SQLQuery(
			$select = "AVG({$bt}PageRating{$bt}.{$bt}Rating{$bt}) RatingAverage, ParentID",
			$from = " {$bt}PageRating{$bt} ",
			$where = "{$bt}ParentID{$bt} = ".$this->owner->ID."",
			$orderby = "RatingAverage DESC",
			$groupby = "{$bt}ParentID{$bt}",
			$having = "",
			$limit = "1"
		);
		return $this->turnSQLIntoDoset($sqlQuery);
	}


	function PageRaterList(){
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$doSet = new DataObjectSet();
    $sqlQuery = new SQLQuery(
			$select = "AVG({$bt}PageRating{$bt}.{$bt}Rating{$bt}) RatingAverage, ParentID",
			$from = " {$bt}PageRating{$bt}, {$bt}SiteTree{$bt}  ",
			$where = "{$bt}ParentID{$bt} = {$bt}SiteTree{$bt}.{$bt}ID{$bt}",
			$orderby = "RatingAverage DESC",
			$groupby = "{$bt}ParentID{$bt}"
		);
		return $this->turnSQLIntoDoset($sqlQuery);

	}

	protected function turnSQLIntoDoset(SQLQuery $sqlQuery) {
		$data = $sqlQuery->execute();
		$doSet = new DataObjectSet();
		if($data) {
			foreach($data as $record) {
				$score = $record["RatingAverage"];
				$stars = ($score);
				if(PageRater::get_round_rating()) {
					$stars = round($stars);
				}
				$percentage = round($score * (100/PageRating::get_number_of_stars()) );
				$reversePercentage = 100 - $percentage;
				$StarClass = PageRating::get_star_entry($stars);
				$record = array(
					'Rating' => "Stars",
					'Stars' => $stars,
					'Percentage' => $percentage,
					'ReversePercentage' => $reversePercentage,
					'Percentage' => $percentage,
					'StarClass' => $StarClass,
					'Page' => DataObject::get_by_id("SiteTree", $record["ParentID"])
				);
				$doSet->push(new ArrayData($record));
			}
			Requirements::themedCSS("PageRater");
		}
		return $doSet;
	}

	function PageHasBeenRatedByUser() {
		return Session::get('PageRated'.$this->owner->ID);
	}

	function NumberOfPageRatings() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$doSet = new DataObjectSet();
    $sqlQuery = new SQLQuery(
			$select = "COUNT({$bt}PageRating{$bt}.{$bt}Rating{$bt}) RatingCount",
			$from = " {$bt}PageRating{$bt} ",
			$where = "{$bt}ParentID{$bt} = ".$this->owner->ID."",
			$orderby = "RatingCount",
			$groupby = "`ParentID`",
			$having = "",
			$limit = "1"
		);
		$data = $sqlQuery->execute();
		if($data) {
			foreach($data as $record) {
				return $record["RatingCount"];
			}
		}
		return 0;
	}

	function requireDefaultRecords() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		parent::requireDefaultRecords();
		$pages = DataObject::get(
			$className = "SiteTree",
			$where = "{$bt}PageRating{$bt}.{$bt}ID{$bt} IS NULL",
			$sort = "",
			$join = "LEFT JOIN {$bt}PageRating{$bt} ON {$bt}PageRating{$bt}.{$bt}ParentID{$bt} = {$bt}SiteTree{$bt}.{$bt}ID{$bt}"
		);
		if($pages) {
			foreach($pages as $page) {
				$count = 0;
				$max = PageRating::get_number_of_stars();
				$goingBackTo = ($max / rand(1, $max)) - 1;
				$stepsBack = $max - $goingBackTo;
				$ratings = PageRater::get_number_of_default_records_to_be_added() / $stepsBack;
				for($i = 1; $i <= $ratings; $i++) {
					for($j = $max; $j > $goingBackTo; $j--) {
						$PageRating = new PageRating();
						$PageRating->Rating = round(rand(1, $j));
						$PageRating->ParentID = $page->ID;
						$PageRating->write();
						$count++;
					}
				}
				DB::alteration_message("Created Initial Ratings for Page with title ".$page->Title.". Ratings created: $count","created");
			}
		}
	}

}

class PageRater_Controller extends Extension {

	function rateagain (){
		Session::set('PageRated'.$this->owner->dataRecord->ID, false);
		Session::clear('PageRated'.$this->owner->dataRecord->ID);
		return array();
	}

	function PageRatingForm() {
		if($this->owner->PageHasBeenRatedByUser()) {
			return false;
		}
		Requirements::javascript('sapphire/thirdparty/jquery-form/form/jquery.form.js');
		Requirements::javascript('mysite/javascript/formSubmit.js');
		$fields = new FieldSet(
			new OptionsetField('Rating', 'Rate '.$this->owner->dataRecord->Title, PageRating::get_star_dropdowndown()),
			new HiddenField('ParentID', "ParentID", $this->ID)
		);
		$actions = new FieldSet(new FormAction('dopagerating', 'Submit'));//
		return new Form($this, 'PageRatingForm', $fields, $actions);
	}

	function dopagerating($data, $form) {
		$data = Convert::raw2sql($data);
		$PageRating = new PageRating();
		$form->saveInto($PageRating);
		$PageRating->write();
		Session::set('PageRated'.$this->owner->dataRecord->ID, true);
		if($this->owner->isAjax()) {
			return $this->owner->renderWith("PageRaterAjaxReturn");
		}
		else {
			Director::redirectBack();
		}
	}


}
