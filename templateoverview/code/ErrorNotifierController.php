<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 **/

class ErrorNotifierController extends Controller {

	protected static  = $email_to_send_error_to;
		static function set_email_to_send_error_to($v) {self::$email_to_send_error_to = $v;}
		static function get_email_to_send_error_to() {return self::$email_to_send_error_to;}

	protected showThankYouNote = false;

	function Form() {
		$form = new Form($this, 'Form',
			new FieldSet(
				new TextField('URL', 'What is the URL of the page the error occured (this is the address shown in the address bar (e.g. http://www.mysite.com/mypage/with/errors/)'),
				new TextareaField('WhatDidYouTryToDo', 'What did you try to do', 3),
				new TextareaField('WhatWentWrong', 'What went wrong', 3),
				new FileField('Screenshot', 'To take a screenshot press the PRT SCR button on your keyboard, then open MS Word or MS Paint and paste the screenshot.  Then attach the file here.')
			),
			new FieldSet(
				new FormAction('senderror', 'Submit Error')
			),
			$requiredFields
		);
		return $form;
	}

	function senderror($data, $form) {
		mail(self::get_email_to_send_error_to(), "error on ".Director::absoluteURL(), print_r($data, true));
		$this->showThankYouNote = true;
		return array();
	}

	function ShowThankYouNote() {
		return $this->showThankYouNote;
	}

}
