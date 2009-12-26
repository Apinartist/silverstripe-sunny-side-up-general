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
	}

	protected static $search_history_object = null;

	function results($data){
		if(isset($data["Search"])) {
			$form = $this->SearchForm();
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
			$data = array(
				'Results' => $form->getResults(),
				'Query' => $form->getSearchQuery(),
				'Recommendations' => $this->Recommendations(),
				'RecommendedSearchPlusSection' => $this->dataRecord->RecommendedSearchPlusSections(),
				'Title' => 'Search Results'
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

	function popularsearchwords() {
		if(!$this->HasPopularSearchWords()) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
			return;
		}
		Requirements::themedCSS("popularsearches");
		$days = intval(Director::URLParam("ID"));
		if(!$days) {
			$days = 100;
		}
		$limit = intval(Director::URLParam("OtherID")+0);
		if(!$limit) $limit++;
		$data = DB::query("SELECT COUNT(`SearchHistoryLog`.`ID`) count, `SearchHistory`.`Title` title, `SearchHistory`.`ID` id FROM `SearchHistoryLog` INNER JOIN `SearchHistory` ON `SearchHistory`.`Title` = `SearchHistoryLog`.`Title` WHERE `SearchHistoryLog`.`Created` > ( NOW() - INTERVAL $days DAY ) GROUP BY `SearchHistoryLog`.`Title` ORDER BY count DESC LIMIT 0, $limit");
		$do = new DataObject();
		$do->Title = "Search Phrase Popularity";
		$do->MenuTitle = "Search Phrase Popularity";
		$do->MetaTitle = "Search Phrase Popularity";
		$do->DataByCount = new DataObjectSet();
		$do->DataByTitle = new DataObjectSet();
		$do->Limit = $limit;
		$do->Days = $days;
		$list = array();
		foreach($data as $key => $row) {
			if(!$key) {
				$max = $row["count"];
			}
			$percentage = floor(($row["count"]/$max)*100);
			$subDataSet = new ArrayData(
				array(
					"ParentID" => $row["id"],
					"Title" => $row["title"],
					"Width" => $percentage,
					"Count" => $row["count"]
				)
			);
			$list[$row["title"]] = $subDataSet;
			$do->DataByCount->push($subDataSet );
		}
		ksort($list);
		foreach($list as $subDataSet ) {
			$do->DataByTitle->push($subDataSet);
		}

		return $this->customise($do)->renderWith(array('SearchPlusPage_popularsearches', 'Page'));
	}

	function Form() {
		return $this->SearchForm("MainSearchForm");
	}

}

