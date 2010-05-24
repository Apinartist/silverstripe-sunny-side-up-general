<?php

class TemplateOverviewTestItem extends DataObject {

	static $db = array(
		"Title" => "Varchar(255)",
		"HowToTestThis" => "Text",
		"Sort" => "Int"
	);

	public static $casting = array(); //adds computed fields that can also have a type (e.g.

	public static $searchable_fields = array(
		"Title" => "PartialMatchFilter",
		"HowToTestThis" => "PartialMatchFilter"
	);

	public static $field_labels = array(
		"Title" => "Name",
		"HowToTestThis" => "How to test this?",
		"Sort" => "Sorting index number",
	);
	public static $summary_fields = array(
		"Title" => "Name"
	); //note no => for relational fields

	public static $singular_name = "Test Item";

	public static $plural_name = "Test Items";
	//CRUD settings

	public static $default_sort = "Sort ASC, Title ASC";

	public static $defaults = array(
		"Sort" => 100
	);//use fieldName => Default Value

	public function populateDefaults() {
		parent::populateDefaults();
		$this->Sort = 100;
	}


}