<?php

/**
 * @author Nicolaas [at] sunnysideup . co .nz
 *
 *
 *
 **/

class SiteWideSettings extends DataObject {

	static $db = array();
	static $has_one = array(
		"WhoLastUpdateIt" => "Member"
	);



	function canCreate($member = null) {
		return !DataObject::get("SiteWideSettings");
	}

	function canDelete($member = null) {
		return false;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if($this->canCreate()) {
			$obj = new SiteWideSettings();
			$obj->write();
		}
	}

	function onBeforeWrite() {
		$currentMemberID = Member::CurrentUserID();
		if($currentMemberID) {
			$this->WhoLastUpdateItID = $currentMemberID;
			parent::onBeforeWrite();
		}
		else {
			return true;
		}

	}

}