<?php


class GoogleMapSearchRecord extends DataObject {

	static $db = array(
		"IPAddres" => "Varchar(32)",
		"SearchedFor" => "Text"
	);

	static $has_one = array(
		"Member" => "Member"
	);

	static function create_new($SearchedFor){
		$obj = new GoogleMapSearchRecord();
		$obj->SearchedFor = $SearchedFor;
		$obj->write();
		return $obj;
	}


	function onBeforeWrite() {
		parent::onBeforeWrite();
		$m = Member::currentMember();
		if($m) {
			$this->MemberID = $m->ID;
		}
		$this->IPAddres = Controller::curr()->getRequest()->getIP();
	}

}
