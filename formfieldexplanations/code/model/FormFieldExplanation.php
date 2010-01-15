<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description contains a list of form field and their explantions
 *
 **/


class FormFieldExplanation extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"Title" => "Varchar(255)",
		"Explanation" => "HTMLText"
	);

	public static $has_one = array(
		"Parent" => "SiteTree"
	);

	public static $searchable_fields = array(
		"Title" => "PartialMatch"
	);

	public static $field_labels = array(
		"Name" => "Field Name",
		"Title" => "Label",
		"Explanation" => "Explanation"
	);
	public static $summary_fields = array(
		"Title" => "Title"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("ParentID");
		$fields->removeByName("Explanation");
		$fields->addFieldToTab("Root.Main", new HTMLEditorField("Explanation", "Explanation", $rows = 3, $cols = 20));
		return $fields;
	}
	/*
	function getFrontEndFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Explanation");
		$fields->addFieldToTab("Root.Main", new HTMLEditorField("Explanation", "Explanation", $rows = 3, $cols = 20));
		return $fields;
	}
	*/

	public static $singular_name = "Form Field Explanation";

	public static $plural_name = "Form Field Explanations";


}