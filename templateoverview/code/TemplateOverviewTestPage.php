<?php

class TemplateOverviewTestPage extends Page {
	//appearance
	static $icon = "mysite/images/treeicons/TemplateOverviewTestPage-file.gif";

	static $allowed_children = "non"; //can also be "none";

	static $default_child = "Blog";

	static $can_be_root = true; //default is true
	//parents and children in classes

}

class TemplateOverviewTestPage_Controller extends Page_Controller {

	function createtest() {
		$tests = DataObject::get("TemplateOverviewTestItem");
		foreach($tests as $test) {
			$entry = new TemplateOverviewTestItemEntry();
			$entry->TemplateOverviewTestItemID = $test->ID;
			$member = Member::currentMemberID();
			$entry->write();
		}
		Director::redirect($this->Link("testscreated"));
	}

	function testscreated() {
		$message = "Test Entries Created";

	}

}