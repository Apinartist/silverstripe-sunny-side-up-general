<?php

class SiteMapPage extends Page {

	static $add_action = 'Intranet Page';

	static $icon = 'mysite/images/treeicons/SiteMapPage';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

}

class SiteMapPage_Controller extends Page_Controller {

	public function init() {
		parent::init();
	}


}

