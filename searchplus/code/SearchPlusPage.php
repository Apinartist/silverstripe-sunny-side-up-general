<?php
/*
 *@author: nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SearchPlusPage extends Page {

	static $add_action = 'Search Plus Page';

	static $can_be_root = true;

	static $icon = 'searchplus/images/treeicons/SearchPlusPage';

	public static $db = array();

	public static $has_many = array(
		"RecommendedSearchPlusSections" => "RecommendedSearchPlusSection"
	);

	public function canCreate() {
		return !DataObject::get_one("SiteTree", 'ClassName = "SearchPlusPage"');
	}

	public function canDelete() {
		return false;
	}

	protected static $result_length = 10;
		static function set_result_length($v) { $v = intval($v); if($v < 1) {user_error("SearchPlusPage::set_result_length expects an integer greater than zero", E_USER_WARNING);} self::$result_length = $v; }
		static function get_result_length() { return self::$result_length; }

	public function getCMSFields($params = null) {
		$fields = parent::getCMSFields($params);
		$fields->addFieldToTab(
			"Root.Content.RecommendedSections",
			new HasManyComplexTableField(
				$controller = $this,
				$name = "RecommendedSearchPlusSections",
				$sourceClass = "RecommendedSearchPlusSection",
				$fieldList = array("Title" => "Title"),
				$detailFormFields = null,
				$sourceFilter = "",
				$sourceSort = "",
				$sourceJoin = ""
			)
		);
		$fields->addFieldToTab(
			"Root.Content.PopularSearchPhrases",
			new LiteralField(
				"PopularSearchPhrasesLink",
				'<p>Please make sure to regular <a href="'.$this->Link().'popularsearchwords/100/10">review the most popular search phrases</a> and to add recommendations for each</a>.</p>'
			)
		);
		return $fields;
	}




}

class SearchPlusPage_Controller extends Page_Controller {

	public function init() {
		parent::init();
		Requirements::javascript("searchplus/javascript/searchpluspage.js");
	}

	protected static $search_history_object = null;

	function Form() {
		return $this->SearchPlusForm("MainSearchForm", "MainSearch", "");
	}

	function results($data){
		if(isset($data["Search"]) || isset($data["MainSearch"])) {
			Requirements::themedCSS("searchpluspage_searchresults");
			if(isset($data["MainSearch"]) || !isset($data["Search"])) {
				$data["Search"] = $data["MainSearch"];
			}
			//redirect if needed
			$data["Search"] = urldecode($data["Search"]);
			$form = $this->SearchPlusForm();
			if(!isset($_GET["redirect"])) {
				self::$search_history_object = SearchHistory::add_entry($data["Search"]);
				if(self::$search_history_object->RedirectTo && self::$search_history_object->RedirectTo != self::$search_history_object->Title) {
					Director::redirect(
						str_replace(
							"Search=".urlencode(self::$search_history_object->Title),
							"Search=".urlencode(self::$search_history_object->RedirectTo),
							HTTP::RAW_setGetVar('redirect', 1, null)
						)
					);
				}
			}
			else {
				self::$search_history_object = SearchHistory::find_entry($data["Search"]);
			}
			//load data for recommended pages
			$recommendationsSet = $this->Recommendations();
			$matchArrayRecommended = array();
			$matchArrayResults = array();
			if($recommendationsSet) {
				foreach($recommendationsSet as $rec) {
					$matchArrayRecommended[$rec->ClassName.$rec->ID] = $rec->ClassName.$rec->ID;
				}
			}
			//work out positions
			$results = $form->getResults();
			$query = $form->getSearchQuery();
			$startingPosition = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
			$endingPosition = $startingPosition + SearchPlusPage::get_result_length();
			$startingPosition++;
			if($results) {
				$total = $results->TotalItems();
			}
			else {
				$total = 0;
			}
			if($endingPosition > $total) {
				$endingPosition = $total;
			}
			//highlight search text and check which ones are recommended
			if($total) {
				foreach($results as $result) {
					$title = $result->getTitle();
					$dbField = DBField::create($className = "Text", $title);
					$result->HighlightedTitle = $dbField->ContextSummary();
					$result->IsRecommended = false;
					$matchArrayResults[$result->ClassName.$result->ID] = $result->ClassName.$result->ID;
					if(isset($matchArrayRecommended[$result->ClassName.$result->ID])) {
						$result->IsRecommended = true;
					}
				}
			}
			$data = array(
				'Results' => $results,
				'Query' => $query,
				'From' => $startingPosition,
				'To' => $endingPosition,
				'Total' => $total,
				'HasResults' => $total ? true : false,
				'Recommendations' => $this->Recommendations(),
				'RecommendedSearchPlusSection' => $this->dataRecord->RecommendedSearchPlusSections(),
				'Title' => 'Search Results',
				'MetaTitle' => 'Search Results for '.Convert::raw2att($query),
				'MenuTitle' => 'Search Results'
			);
			return $this->customise($data)->renderWith(array('SearchPlusPage_results', 'Page'));
		}
		return Array();
	}

	function Recommendations() {
		if(self::$search_history_object) {
			return self::$search_history_object->Recommendations();
		}
	}

	function HasPopularSearchWords() {
		return Permission::check("ADMIN");
	}

	function PopularSearchWordsForAllUsers($days = 100, $limit = 7) {
		$do = $this->getPopularSearchWords($days, $limit, $mergeRedirects = true);
		return $do->DataByCount;
	}

	function popularsearchwords(HTTPRequest $HTTPRequest) {
		if(!$this->HasPopularSearchWords()) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
			return;
		}
		Requirements::themedCSS("popularsearches");
		$days = intval($HTTPRequest->param("ID"));
		if(!$days) {
			$days = 100;
		}
		$limit = intval($HTTPRequest->param("OtherID")+0);
		if(!$limit) $limit++;
		$do = $this->getPopularSearchWords($days, $limit);
		return $this->customise($do)->renderWith(array('SearchPlusPage_popularsearches', 'Page'));
	}

	protected function getPopularSearchWords($days, $limit, $mergeRedirects = false) {
		$extraWhere = '';
		if($mergeRedirects) {
			$extraWhere = " AND `RedirectTo` = '' OR `RedirectTo` IS NULL";
		}
		$data = DB::query("
			SELECT COUNT(`SearchHistoryLog`.`ID`) count, `SearchHistory`.`RedirectTo` RedirectTo, `SearchHistory`.`Title` title, `SearchHistory`.`ID` id
			FROM `SearchHistoryLog`
				INNER JOIN `SearchHistory` ON `SearchHistory`.`Title` = `SearchHistoryLog`.`Title`
			WHERE `SearchHistoryLog`.`Created` > ( NOW() - INTERVAL $days DAY ) ".$extraWhere."
			GROUP BY `SearchHistoryLog`.`Title` ORDER BY count DESC LIMIT 0, $limit");
		$do = new DataObject();
		$title = "Search Phrase Popularity, $days days $limit entries";
		$do->Title = $title;
		$do->MenuTitle = $title;
		$do->MetaTitle = $title;
		$do->DataByCount = new DataObjectSet();
		$do->DataByTitle = new DataObjectSet();
		$do->Limit = $limit;
		$do->Days = $days;
		$list = array();
		foreach($data as $key => $row) {
			if(!$key) {
				$max = $row["count"];
			}
			if($mergeRedirects) {
				$data = DB::query("
					SELECT COUNT(`SearchHistoryLog`.`ID`) count
					FROM `SearchHistoryLog`
						INNER JOIN `SearchHistory` ON `SearchHistory`.`Title` = `SearchHistoryLog`.`Title`
					WHERE `SearchHistoryLog`.`Created` > ( NOW() - INTERVAL $days DAY ) AND `SearchHistory`.`RedirectTo` = '".$row["title"]."'
					GROUP BY `SearchHistory`.`RedirectTo` ORDER BY count DESC LIMIT 1");
				if($data) {
					$extraCounts = $data->value();
				}
				$row["count"] += $extraCounts;
			}
			$percentage = floor(($row["count"]/$max)*100);
			$subDataSet = new ArrayData(
				array(
					"ParentID" => $row["id"],
					"Title" => $row["title"],
					"Width" => $percentage,
					"Count" => $row["count"],
					"Link" => $this->Link()."results/?Search=".urldecode($row["title"])."&amp;action_results=Search"
				)
			);
			$list[$row["title"]] = $subDataSet;
			$do->DataByCount->push($subDataSet );
		}
		ksort($list);
		foreach($list as $subDataSet ) {
			$do->DataByTitle->push($subDataSet);
		}
		return $do;
	}


}

