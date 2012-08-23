<?php
/**
 * EditableTextField
 *
 * This control represents a user-defined text field in a user defined form
 *
 * @package userforms
 */

class EditableOffSpringField extends EditableFormField {

	static $singular_name = 'Offspring field';

	static $plural_name = 'Offspring fields';

	function getFieldConfiguration() {
		$fields = parent::getFieldConfiguration();
		return $fields;
	}

	function getFormField() {
		return new OffSpringField($this->Name, $this->Title);
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
		$value = (isset($data[$this->Name])) ? $data[$this->Name] : false;
		if($value) {
			$name = isset($value["name"]) ? $value["name"] : "-- not entered --";
			$dob = isset($value["dob"]) ? $value["dob"] : "-- not entered --";
			$sex = isset($value["sex"]) ? $value["sex"] : "-- not entered --";
			return "Name: $name | dob: $dob | sex: $sex";
		}
	}

	public function Icon() {
		return 'userforms_relatives/images/icons' . strtolower($this->class) . '.png';
	}

}
