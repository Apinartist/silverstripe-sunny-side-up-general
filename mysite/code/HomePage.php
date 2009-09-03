<?php

class HomePage extends Page {

	public function canCreate($member = null) {
		return DataObject::get_one('HomePage') == null;
	}
}

class HomePage_Controller extends Page_Controller {
}
