<?php

/**
* @author Nicolaas Francken
*
*
*
**/

class SiteWideSettings extends DataObject {

	static $db = array();
	static $has_one = array();

	/* no idea why we need all this */
	static $url_base = "sitewidesettings";
	static $url_segment;
	static $url_rule;
	static $url_priority;
	static $menu_priority;


	function url_base() {
		return self::$url_base;
	}

	function canCreate($member) {
		return !DataObject::get("SiteWideSettings");
	}

	function canDelete($member) {
		return false;
	}

	function RequiredFields() {
		if($this->canCreate()) {
			$obj = new SiteWideSettings();
			$obj->write();
		}
	}

}