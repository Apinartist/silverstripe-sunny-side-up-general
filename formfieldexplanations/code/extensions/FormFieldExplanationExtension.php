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
					$message = '';
					if(isset($explanations[$name])) {
						$message .= $explanations[$name];
						if($datarecord->canEdit()) {
							$message .= ' | '.self::CMSLink($datarecord->ID);
						}
					}
					elseif($datarecord->canEdit() && $name) {
						$title = $field->Title();
						if(!$title) {
							$title = $name;
						}
						$message .= ' | <a href="'.$datarecord->Link().'addfieldexplanation/'.urlencode($name).'/'.urlencode($title).'/" class="addFieldExplanation">add explanation</a>';
					}
					$do = true;
					switch($field->class) {
						case "HeaderField":
							$do = false;
							break;
						default:
							break;
					}
					$id = $field->id();
					$message = str_replace("/", "\/", Convert::raw2js($message));
					if($do && $message && $name && $id) {
						$js .= "
						formfieldexplanations.add_info('".$name."', '".$message."', '".$id."');";
					}
				}
			}
		}
		Requirements::javascript("formfieldexplanations/javascript/formfieldexplanations.js");
		Requirements::customScript($js, "FormFieldExplanationExtension");
		Requirements::themedCSS("formfieldexplanations");
		return $form;
	}

	public function addfieldexplanation(HTTPRequest $HTTPRequest) {
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

	protected function editfieldexplanation(HTTPRequest $HTTPRequest) {
		$customiseArray = array(
			"Title" => "Test",
			"Form" => "FormTest"
		);
		//TO DO!!!!! link with DataObjectsorter
		return Array();
	}

	protected static function CMSLink ($id) {
		return '<a href="admin/show/'.$id.'" class="editFieldExplanation">edit description in CMS</a>';
	}

}