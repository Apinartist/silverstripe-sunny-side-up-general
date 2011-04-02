<?php
/**
 * Provides an {@link FormField} which allows form to validate for non-bot submissions
 * by giving them a honey pot
 *
 * @module SimplestSpam
 */
class InvisibleSpamField extends SpamProtectorField {

	protected static $definitions = array(
		"Email" => array("Class" => "mustenterbecausitisrequired", "Name" => "your_email", "Label" => "extra email"),
		"URL" => array("Class" => "urlthatisrequired", "Name" => "your_url", "Label" => "extra url"),
		"BLANK" => array("Class" => "leavethisblank", "Name" => "BLANK", "Label" => "Please leave this field blank to stop spam")
	);
		static function set_definitions($a) {self::$definitions = $a;}
		static function get_definitions() {return self::$definitions;}
		static function add_definition($key, $class, $name, $label) {
			self::$definitions[$key] = array(
				"Class" => $class,
				"Name" => $name,
				"Label" => $label
			);
		}

	protected static $used_field = "Email";
		static function set_used_field($k) {self::$used_field = $k;}
		static function get_used_field() {return self::$used_field;}

	protected static $css_rules = array(
		"position" => "absolute",
		"text-indent" => "-2000px"
	);
		static function set_css_rules($a) {self::$css_rules = $a;}
		static function get_css_rules() {return self::$css_rules;}

	protected function labelUsed() {
		return self::$definitions[self::$used_field]["Label"];
	}

	protected function fieldNameUsed() {
		return self::$definitions[self::$used_field]["Name"];
	}

	protected function classNameUsed() {
		return self::$definitions[self::$used_field]["Class"];
	}

	function FieldHolder() {
		if(is_array(self::$css_rules) && count(self::$css_rules)) {
			$css = '';
			foreach(self::$css_rules as $key => $value) {
				$css .= '.css("'.$key.'", "'.$value.'")';
			}
			Requirements::customScript('jQuery(".'.$this->classNameUsed().'")'.$css.';', $this->classNameUsed);
		}
		$Title = $this->labelUsed();
		$Message = $this->XML_val('Message');
		$MessageType = $this->XML_val('MessageType');
		$Type = $this->XML_val('Type');
		$extraClass = $this->XML_val('extraClass');
		$Name = $this->fieldNameUsed();
		$Field = $this->XML_val('Field');
		$messageBlock = (!empty($Message)) ? "<span class=\"message $MessageType\">$Message</span>" : "";
		$name = $this->labelUsed();
		return <<<HTML
<div id="$Name" class="mustenterbecausitisrequired">
	<label>$Title</label>
	<div class="middleColumn">
		{$Field}
	</div>
</div>
HTML;
	}

	public function Field() {
		$this->initialise();
		$html = '<input type="text" name="'.$this->fieldNameUsed().'" class="text" />';
		return $html;
	}


	public function validate($validator) {
		// don't bother querying the SimplestSpam-service if fields were empty
		if(!isset($_REQUEST[$this->fieldNameUsed()]) || $_REQUEST[$this->fieldNameUsed()]) {
			$validator->validationError(
				$this->name,
				$this->createValidationMessage(),
				"validation",
				false
			);
			return false;
		}
		return true;
	}

	protected function  createValidationMessage() {
		return
			_t("InvisibleSpamField.SPAMMESSAGE_NOTSUBMITTED", "Form could not be submitted. ").
			_t("InvisibleSpamField.SPAMMESSAGE_DONOTCOMPLETE1", " Please do NOT complete the [<i>").
			$this->labelUsed().
			_t("InvisibleSpamField.SPAMMESSAGE_DONOTCOMPLETE2", "</i>] field. This field is added to prevent spam (Spammers will complete it - you should not complete it).");

	}


	protected function initialise() {
		return true;
	}
}

