<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description contains a list of form field and their explantions
 *
 **/


class FormFieldExplanationExtension extends Extension{

	static $allowed_actions = array("addfieldexplanation");

	static function add_explanations($form, $datarecord) {
		$js = '';
		$dos = DataObject::get("FormFieldExplanation", "`ParentID` = ".$datarecord->ID);
		$explanations = array();
		if($dos) {
			foreach($dos as $do) {
				$explanations[$do->Name] = $do->Explanation;
			}
		}
		$dos = $do = null;
		$dataFields = $form->fields();
		if($dataFields){
			foreach($dataFields as $field) {
				if($name = $field->Name()) {
					$rightTitle = $field->RightTitle();
					if(isset($explanations[$name])) {
						$rightTitle .= $explanations[$name];
						if($datarecord->canEdit()) {
							$rightTitle .= ' | '.self::CMSLink($datarecord->ID);
						}
					}
					elseif($datarecord->canEdit() && $name) {
						$title = $field->Title();
						if(!$title) {
							$title = $name;
						}
						$rightTitle .= ' | <a href="'.$datarecord->Link().'addfieldexplanation/'.urlencode($name).'/'.urlencode($title).'/" class="addFieldExplanation">add explanation</a>';
					}
					$do = true;
					switch($field->class) {
						case "HeaderField":
							$do = false;
							break;
						default:


					}
					if($do) {
						$js .= "
						formfieldexplanations.add_info('".$name."', '".str_replace("/", "\/", Convert::raw2js($rightTitle))."', '".$field->id()."');";
					}
				}
			}
		}
		Requirements::javascript("formfieldexplanations/javascript/formfieldexplanations.js");
		Requirements::customScript($js, "FormFieldExplanationExtension");
		Requirements::themedCSS("formfieldexplanations");
		return $form;
	}

	protected function addfieldexplanation(HTTPRequest $HTTPRequest) {
		$fieldName = $HTTPRequest->param("ID");
		$fieldTitle = $HTTPRequest->param("OtherID");
		$obj = DataObject::get_one("FormFieldExplanation", "`Name` = '".$fieldName."' AND ParentID = ".$this->owner->ID);
		if(!$obj) {
			$obj = new FormFieldExplanation();
		}
		$obj->Name = $fieldName;
		$obj->Title = $fieldTitle;
		$obj->Explanation = "explanation to be added";
		$obj->ParentID = $this->owner->ID;
		$obj->write();
		if(Director::is_ajax()) {
			return self::CMSLink($this->owner->ID);
		}
		else {
			Director::redirectBack();
		}
	}

	protected static function CMSLink ($id) {
		return '<a href="admin/show/'.$id.'" class="editFieldExplanation">edit description in CMS</a>';
	}

}