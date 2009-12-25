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

class RecommendedSearchPlusSection Extends DataObject {

	static $db = array(
		"Title" => "Varchar(255)",
		"Intro" => "HTMLText",
		"Sort" => "Int"
	);

	static $has_one = array(
		"ParentPage" => "Page"
	);

	static $singular_name = 'Recommended SearchPlus Section';

	static $plural_name = 'Recommended SearchPlus Sections';

	static $default_sort = 'Sort, Title';

	public static $searchable_fields = array(
		"Title"
	);

	public static $summary_fields = array(
		"Title", "Sort"
	);

	public static $field_labels = array(
		"Sort" => "Sort Index"
	);

}