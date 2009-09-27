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
	static $has_many = array();
	static $indexes = array();
	static $defaults = array();
	static $has_many = array();
	static $many_many = array();
	static $belongs_many_many = array();
	static $many_many_extraFields = array();
	static $searchable_fields = array();


	function canCreate($member) {
		return !DataObject::get("SiteWideSettings");
	}

	function canDelete($member) {
		return false;
	}

}