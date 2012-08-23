<?php
/**
 * EditableTextField
 *
 * This control represents a user-defined text field in a user defined form
 *
 * @package userforms
 */

class EditableAncestryField extends EditableFormField {

	static $singular_name = 'Ancestry field';

	static $plural_name = 'Ancestry fields';

	function getFieldConfiguration() {
		$fields = parent::getFieldConfiguration();
		return $fields;
	}

	function getFormField() {
		return new AncestryField($this->Name, $this->Title);
	}

	/**
	 * Return the validation information related to this field. This is
	 * interrupted as a JSON object for validate plugin and used in the
	 * PHP.
	 *
	 * @see http://docs.jquery.com/Plugins/Validation/Methods
	 * @return Array
	 */
	public function getValidation() {
		$options = array();
		return $options;
	}

	/**
	 * Return the Value of this Field
	 *
	 * @return String
	 */
	function getValueFromData($data) {
		$returnValue = "";
		$value = (isset($data[$this->Name])) ? $data[$this->Name] : false;
		if($value) {
			if(is_array($value)) {
				foreach($value as $key => $dataEntered) {
					$key = str_replace("Field", "", $key);
					$key = str_replace("m", "mother-", $key);
					$key = str_replace("f", "father-", $key);
					if(!$dataEntered) {
						$dataEntered = "---";
					}
					$returnValue .= "<br />$key: $dataEntered ";
				}
			}
		}
		return $returnValue;
	}

	public function Icon() {
		return 'userforms_relatives/images/icons' . strtolower($this->class) . '.png';
	}

}
