<?php

/**
 * Protecter class to handle spam protection interface
 *
 * @package recaptcha
 */

class SimplestSpamProtector implements SpamProtector {

	protected $field;

	/**
	 * Return the Field that we will use in this protector
	 *
	 * @return string
	 */
	function getFieldName() {
		return 'SimplestSpamField';
	}

	/**
	 * @return bool
	 */
	function updateForm($form, $before=null, $fieldsToSpamServiceMapping=null) {

		$this->field = $this->getFormField();

		if ($before && $form->Fields()->fieldByName($before)) {
			$form->Fields()->insertBefore($this->field, $before);
		}
		else {
			$form->Fields()->push($this->field);
		}

		return $form->Fields();
	}

	function setFieldMapping($fieldToPostTitle, $fieldsToPostBody=null, $fieldToAuthorName=null, $fieldToAuthorUrl=null, $fieldToAuthorEmail=null, $fieldToAuthorOpenId=null) {

	}


	function getFormField() {
		return new SimplestSpamField("SimplestSpamField", "Please answer this question to prove you are a real human", null, $form);
	}

	function sendFeedback($object = null, $feedback = "") {
		return true;
	}

}
