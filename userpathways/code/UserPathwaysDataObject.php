<?php

class UserPathwaysDataObject extends DataObject {

	static $db = array(
		"SID" => "Varchar(32)",
		"VisitedIDs" => "Text"
	);

	static $indexes = array(
		"SID" => "unique (SID)"
	);

	static $done = false;

	static function add_current_page() {
		if(!self::$done) {
			$currentPage = Director::currentPage();
			if($currentPage) {
				$currentPageID = $currentPage->ID;
				if($obj = DataObject::get("UserPathwaysDataObject", 'SID ="'.session_id().'"')) {
					$obj->VisitedIDs = $obj->VisitedIDs.",".$currentPageID;
				}
				else {
					$obj = new UserPathwaysDataObject();
					$obj->SID = session_id();
					$obj->VisitedIDs = $currentPageID;
				}
				$obj->write();
				self::$done = true;
			}
		}
	}
}