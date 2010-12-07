<?php
/**
 * Provides an {@link FormField} which allows form to validate for non-bot submissions
 * by giving them a honey pot
 *
 * @module SimplestSpam
 */
class InvisibleSpamField extends SpamProtectorField {

	public function Field() {
		$this->initialise();
		$html = '<input type="text" name="linkurlwebsite" class="text" />';
		return $html;
	}

	function FieldHolder() {
		$Title = $this->XML_val('Title');
		$Message = $this->XML_val('Message');
		$MessageType = $this->XML_val('MessageType');
		$Type = $this->XML_val('Type');
		$extraClass = $this->XML_val('extraClass');
		$Name = $this->XML_val('Name');
		$Field = $this->XML_val('Field');
		$messageBlock = (!empty($Message)) ? "<span class=\"message $MessageType\">$Message</span>" : "";
		return <<<HTML
<div id="$Name" style="display: inline; text-indent: -2000px; position: absolute; top: -100px; left: -100px;">
	<label>website URL link  </label>
	<div class="middleColumn">
		{$Field}
	</div>
</div>
HTML;
	}

	public function validate($validator) {
		// don't bother querying the SimplestSpam-service if fields were empty
		if(!isset($_REQUEST['linkurlwebsite']) || $_REQUEST['linkurlwebsite']) {
			$validator->validationError(
				$this->name,
				_t("InvisibleSpamField.SPAMMESSAGE", "expected spam attempt"),
				"validation",
				false
			);
			return false;
		}
		return true;
	}


	protected function initialise() {
		return true;
	}

}
