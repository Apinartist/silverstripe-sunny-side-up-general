<?php

class TemplateOverviewTestPage extends Page {

	//parents and children in sitetree
	static $allowed_children = "none"; //can also be "none";
	static $can_be_root = false;

	//appearance
	static $icon = "templateoverview/images/treeicons/TemplateOverviewTestPage";


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
