<?php
/**
 * EditableCheckbox
 * A user modifiable checkbox on a UserDefinedForm
 *
 * @package userforms
 */

class EditableTermsAndConditionsCheckbox extends EditableFormField {

	static $singular_name = 'Terms and Conditions Checkbox';

	static $plural_name = 'Terms and Conditions Checkboxes';

	static $has_one = array(
		"TandCPage" => "SiteTree"
	);

	public function getFieldConfiguration() {
		$options = parent::getFieldConfiguration();
		$options->push(new CheckboxField("Fields[$this->ID][CustomSettings][Default]", _t('EditableFormField.CHECKEDBYDEFAULT', 'Checked by Default?'), $this->getSetting('Default')));
		$options->push(new DropdownField("Fields[$this->ID][CustomSettings][TandCPageID]", "What is your Terms and Conditions page?  This will be added as a link to the end of your field title.", DataObject::get("SiteTree", "ParentID < 1")));
		return $options;
	}

	public function getFormField() {
		$page = $this->getSetting('TandCPage');
		return new CheckboxField( $this->Name, $this->Title.': <a href="'.$page->Link.'" rel="external">'.$page->Title.'</a>', $this->getSetting('Default'));
	}

	public function getValueFromData($data) {
		$value = (isset($data[$this->Name])) ? $data[$this->Name] : false;
		return ($value) ? _t('EditableFormField.YES', 'Yes') : _t('EditableFormField.NO', 'No');
	}
}
