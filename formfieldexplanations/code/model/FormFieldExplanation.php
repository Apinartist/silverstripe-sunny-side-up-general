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

	public static $singular_name = "Form Field Explanation";

	public static $plural_name = "Form Field Explanations";
	/*
	function requiredDefaultRecords(){
		parent::requiredDefaultRecords();
		$array = self::$classname_function_combo;
		if(count($array)) {
			foreach($array as $arr	) {
				$className = $arr["ClassName"];
				$functionName = $arr["FunctionName"];
				if(class_exists($className)) {
					$obj = singleton($className);
					if(method_exists($obj, $functionName)) {
						$form = $obj->$functionName;
						$dataFields = $form->fields->dataFields();
						if($dataFields){
							foreach($dataFields as $field) {
								if($name = $field->Name()) {
									$obj = DataObject::get_one("FormFieldExplanation", "`Name` '".$name."'");
									if(!$obj) {
										$obj = new FormFieldExplanation();
									}
									$title = $field->Title();
									if(!$title) {
										$title = $name;
									}
									$obj->Title = $title;
									$obj->ParentClassName = $className;
									$obj->write();
								}
							}
						}
					}
				}
			}
		}
	}
*/

}