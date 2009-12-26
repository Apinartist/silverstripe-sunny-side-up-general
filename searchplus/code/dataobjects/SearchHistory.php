<?php
/**
 *@author: nicolaas[at]sunnysideup.co.nz
 *@description:
 * a log history and counted history of searches done (e.g. 100 people searched for "sunshine")
 * it allows gives the opportunity to link zero or more pages to a particular search phrase
 *
 *
 *
 **/

class SearchHistory Extends DataObject {

	static $db = array(
		"Title" => "Varchar(255)",
		"RedirectTo" => "Varchar(255)"
	);

	static $many_many = array(
		"Recommendations" => "Page"
	);

	static $singular_name = 'Search History Phrase';

	static $plural_name = 'Search History Phrases';

	static $default_sort = 'Title';

	public static $searchable_fields = array(
		"Title",
		"RedirectTo"
	);

	public static $summary_fields = array(
		"Title", "RedirectTo"
	);

	public static $field_labels = array(
		"Title" => "Phrase Searched For",
		"RedirectTo" => "Redirect To"
	);


	function canDelete() {
		return false;
	}

	static function add_entry($KeywordString) {
		$KeywordString = self::clean_keywordstring($KeywordString);
		$obj = new SearchHistoryLog();
		$obj->Title = $KeywordString;
		$obj->write();
		if($obj = DataObject::get_one("SearchHistory", "`Title` = '".$KeywordString."'")) {
			//do nothing
		}
		else {
			$obj = new SearchHistory();
			$obj->Title = $KeywordString;
			$obj->write();
		}
		return $obj;
	}

	static function find_entry($KeywordString) {
		$KeywordString = self::clean_keywordstring($KeywordString);
		return DataObject::get_one("SearchHistory", "`Title` = '".$KeywordString."'");
	}

	static function clean_keywordstring($KeywordString) {
		Convert::raw2sql($KeywordString);
		$KeywordString = trim(eregi_replace(" +", " ", $KeywordString));
		return $KeywordString;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Title");
		$fields->addFieldToTab("Root.Main", new HeaderField($name = "TitleHeader", $this->Title, 3), "RedirectTo");
		$fields->removeByName("Recommendations");
		if(!$this->RedirectTo) {
			$source = DataObject::get("Page", "`ShowInSearch` = 1 AND `ClassName` <> 'SearchPlusPage'");
			$sourceArray = $source->toDropdownMap();
			$fields->addFieldToTab("Root.Main", new MultiSelectField($name = "Recommendations", $title = "Recommendations", $sourceArray));
		}
		else {
			$fields->addFieldToTab("Root.Main", new LiteralField($name = "Recommendations", '<p>This search phrase cannot have recommendations, because it redirects to <i>'.$this->RedirectTo.'</i></p>'));
		}
		$page = DataObject::get_one("SearchPlusPage");
		if(!$page) {
			user_error("Make sure to create a SearchPlusPage to make proper use of this module", E_USER_NOTICE);
		}
		else {
			$fields->addFieldToTab("Root.Main", new LiteralField($name = "BackLinks", '
				<p>
					Review a graph of all <a href="'.$page->Link().'popularsearchwords/100/10/">Popular Search Phrases</a> OR
					You may want to <a href="'.$page->Link().'results/?Search='.urlencode($this->Title).'&amp;action_results=Search&amp;redirect=1">try this search</a>.
				</p>'
			));
		}
		return $fields;
	}



}



class SearchHistoryLog Extends DataObject {

	static $db = array(
		"Title" => "Varchar(255)"
	);

	static $singular_name = 'Search History Log Entry';

	static $plural_name = 'Search History Log Entries';

	static $default_sort = 'Created DESC';

	public static $searchable_fields = array(
		"Title"
	);

	public static $summary_fields = array(
		"Title"
	);

	public static $field_labels = array(
		"Search Phrase Entered"
	);

	function canCreate() {
		return false;
	}

	function canDelete() {
		return false;
	}


}