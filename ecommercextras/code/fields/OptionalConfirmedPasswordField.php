<?php
/**
 * Password input field.
 * @author nicolaas[at]sunnysideup.co.nz
 * @package forms
 * @subpackage ecommercextras
 */

class OptionalConfirmedPasswordField extends ConfirmedPasswordField {

	function __construct($name, $title = null, $value = "", $form = null, $showOnClick = false, $titleConfirmField = null) {
		parent::__construct($name, $title, $value, $form, $showOnClick, $titleConfirmField);
	}

	function Field() {
		Requirements::javascript('ecommercextras/javascript/OptionalConfirmedPasswordField.js');
		Requirements::block(SAPPHIRE_DIR . '/javascript/ConfirmedPasswordField.js');
		return parent::Field();
	}
}
