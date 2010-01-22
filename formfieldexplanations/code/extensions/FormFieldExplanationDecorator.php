<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description contains a list of form field and their explantions
 *
 **/


class FormFieldExplanationDecorator extends DataObjectDecorator{

	public function extraStatics() {
		return array (
			'has_many' => array(
				'FormFieldExplanation' => 'FormFieldExplanation'
			)
		);
	}

	/*
	function updateCMSFields(FieldSet &$fields) {
		if(DataObject::get_one("FormFieldExplanation", "`ParentID` = ".$this->owner->ID)) {
			$fields->addFieldToTab("Root.Content.FormExplanations", $this->getFormFieldExplanationHasManyTable());
		}
		return $fields;
	}
	*/

	function getFormFieldExplanationHasManyTable() {
		$field = new HasManyComplexTableField(
			$controller = $this->owner,
			$name = "FormFieldExplanation",
			$sourceClass = "FormFieldExplanation",
			$fieldList = array("Title" => "Title"),
			$detailFormFields = null,
			$sourceFilter = "`ParentID` =".$this->owner->ID,
			$sourceSort = "",
			$sourceJoin = ""
		);
		$field->setPermissions(array("edit", "delete"));
		$field->setParentClass($this->owner->class);
		$field->relationAutoSetting = true;
		return $field;
	}
}