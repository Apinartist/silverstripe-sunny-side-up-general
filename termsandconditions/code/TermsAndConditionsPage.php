<?php

class TermsAndConditionsPage extends Page {

	static $icon = "mysite/images/treeicons/TermsAndConditionsPage";

	public function canCreate($member = null) {
		return DataObject::get_one('TermsAndConditionsPage') == null;
	}
}

class TermsAndConditionsPage_Controller extends Page_Controller {
}
