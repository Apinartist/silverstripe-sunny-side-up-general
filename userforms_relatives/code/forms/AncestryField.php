<?php
/**
 *
 * # Configuration
 *
 * All options in {@link setConfig()} are passed through to {@link nameField} and {@link dobField}.
 *
 * @package sapphire
 * @subpackage forms
 */
class AncestryField extends FormField {

	/**
	 * @var nameField
	 */
	//level 1
	protected $fieldDefinition = array(
		"mField" => "Mother",
		"fField" => "Father",
	//level 2
		"mmField" => "Mother's Mother",
		"mfField" => "Mother's Father",
		"fmField" => "Father's Mother",
		"ffField" => "Father's Father",
	//level 3
		"mmmField" => "Mother's Mother's Mother",
		"mmfField" => "Mother's Mother's Father",
		"mfmField" => "Mother's Father's Mother",
		"mffField" => "Mother's Father's Father",
		"fmmField" => "Father's Mother's Mother",
		"fmfField" => "Father's Mother's Father",
		"ffmField" => "Father's Father's Mother",
		"fffField" => "Father's Father's Father",
	//level 4
		"mmmmField" => "Mother's Mother's Mother's Mother",
		"mmmfField" => "Mother's Mother's Mother's Father",
		"mmfmField" => "Mother's Mother's Father's Mother",
		"mmffField" => "Mother's Mother's Father's Father",
		"mfmmField" => "Mother's Father's Mother's Mother",
		"mfmfField" => "Mother's Father's Mother's Father",
		"mffmField" => "Mother's Father's Father's Mother",
		"mfffField" => "Mother's Father's Father's Father",
		"fmmmField" => "Father's Mother's Mother's Mother",
		"fmmfField" => "Father's Mother's Mother's Father",
		"fmfmField" => "Father's Mother's Father's Mother",
		"fmffField" => "Father's Mother's Father's Father",
		"ffmmField" => "Father's Father's Mother's Mother",
		"ffmfField" => "Father's Father's Mother's Father",
		"fffmField" => "Father's Father's Father's Mother",
		"ffffField" => "Father's Father's Father's Father"
	);

	protected $fieldHolder = array();

	function __construct($name, $title = null, $value = ""){
		foreach($this->fieldDefinition as $key => $fieldTitle) {
			$this->fieldHolder[$key] = new TextField($name . '['.$key.']', $fieldTitle);
		}
		parent::__construct($name, $title, $value);
	}

	function setForm($form) {
		parent::setForm($form);
		foreach($this->fieldDefinition as $key => $fieldTitle) {
			$this->fieldHolder[$key]->setForm($form);
		}
	}

	function Field() {
		Requirements::themedCSS("AncestryField");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript("userforms_relatives/javascript/AncestryField.js");
		$html = "";
		foreach($this->fieldDefinition as $key => $fieldTitle) {
			$levelClass = "level".(strlen($key)-5);
			$nextLevels = ".".str_replace("Field", "mField", $key).", .".str_replace("Field", "fField", $key);
			$html .= "<div class=\"$key $levelClass ancestorNode \" rel=\"$nextLevels\">".$this->fieldHolder[$key]->SmallFieldHolder()."</div>";
		}
		return $html;
	}

	/**
	 * Sets the internal value to ISO date format.
	 *
	 * @param string|array $val String expects an ISO date format. Array notation with 'date' and 'time'
	 *  keys can contain localized strings. If the 'dmyfields' option is used for {@link nameField},
	 *  the 'date' value may contain array notation was well (see {@link nameField->setValue()}).
	 */
	function setValue($val) {
		if(empty($val)) {
			foreach($this->fieldDefinition as $key => $fieldTitle) {
				$this->fieldHolder[$key]->setValue(null);
			}
		}
		else {
			// String setting is only possible from the database, so we don't allow anything but ISO format
			if(is_string($val)) {
				//TO DO
			}
			// Setting from form submission
			elseif(is_array($val)) {
				foreach($this->fieldDefinition as $key => $fieldTitle) {
					$myValue = isset($val[$key]) ? $val[$key] : "";
					$this->fieldHolder[$key]->setValue($myValue);
				}
			}
			else {
				$this->nameField->setValue($val);
				$this->dobField->setValue($val);
				$this->sexField->setValue($val);
			}
		}
	}

	function dataValue() {
		$array = array();
		foreach($this->fieldDefinition as $key => $fieldTitle) {
			$array[$key] = $this->fieldHolder[$key]->dataValue();
		}
		return $array;
	}

	public function Icon() {
		return 'userforms/images/' . strtolower($this->class) . '.png';
	}

}

