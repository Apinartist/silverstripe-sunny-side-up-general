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
	);

	static $many_many = array(
		"Recommendations" => "Page"
	);

	static $casting = array(
		"Count" => "Int"
	);

	static function add_entry($KeywordString) {
		Convert::raw2sql($KeywordString);
		$KeywordString = trim(eregi_replace(" +", " ", $KeywordString));
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

	static $singular_name = 'Search History Phrase';

	static $plural_name = 'Search History Phrases';

	static $default_sort = 'Title';

	public static $searchable_fields = array(
		"Title"
	);

	public static $summary_fields = array(
		"Title", "Count"
	);

	public static $field_labels = array(
		"Phrase Searched For"
	);


	function canDelete() {
		return false;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Recommendations");
		$fields->push(new TreeMultiselectField($name = "Recommendations", $title = "Recommendations", $sourceObject = "SiteTree", $keyField = "ID", $labelField = "Title"));
		return $fields;
	}

	function getCount() {
		DataObject::get("SearchHistoryLog", "`Title` = '".$this->Title."'");
	}

	function Count() {
		return $this->getCount();
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