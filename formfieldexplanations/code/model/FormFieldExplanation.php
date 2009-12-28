<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description contains a list of form field and their explantions
 *
 **/


class FormFieldExplanation extends DataObject {

	protected static $classname_function_combo = array();
		static function add_classname_function_combo($className, $functionName) {
			self::$classname_function_combo[] = array("ClassName" => $className, "FunctionName" => $functionName);
		}

	public static $db = array(
		"ParentClassName" => "Varchar(255)",
		"Code" => "Varchar(255)",
		"Title" => "Varchar(255)",
		"Explanation" => "HTMLText"
	);

	public static $searchable_fields = array(
		"Code" => "Field Code",
		"Title" => "Field Label",
		"Explanation" => "Explanation"
	);
	public static $field_labels = array(
		"Name" => "Carrot Name"
	);
	public static $summary_fields = array(
		"Parent.Title" => "Page",
		"Title" => "Title"
	);

	public static $singular_name = "Form Field Explanation";

	public static $plural_name = "Form Field Explanations";

	function requiredDefaultRecords(){
		parent::requiredDefaultRecords();
		$array = self::$classname_function_combo;
		if(count($array)) {
			foreach($array as $arr	) {
				$className = $arr["ClassName"];
				$functionName = $arr["FunctionName"]
				if(class_exists($className)) {
					$obj = singleton($className);
					if(method_exists($obj, $functionName)) {
						$form = $obj->$functionName;
						$dataFields = $form->fields->dataFields();
						if($dataFields){
							foreach($dataFields as $field) {
								if($name = $field->Name()) {
									$obj = DataObject::get_one("FormFieldExplanation", "`Code` '".$name."'");
									if(!$obj) {
										$obj = new FormFieldExplanation();
									}
									if($title = $field->Title()) {
										//do nothing;
									}
									else {
										$title = $field->Title();
									}
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


}