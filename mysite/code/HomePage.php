<?php

class HomePage extends Page {

	static $icon = "mysite/images/treeicons/HomePage";

	public function canCreate($member = null) {
		return DataObject::get_one('HomePage') == null;
	}
}

class HomePage_Controller extends Page_Controller {
}
