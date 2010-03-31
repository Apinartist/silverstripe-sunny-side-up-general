<?php

class EcommerceVote extends DataObject {

	public static $db = array(
		"SessionID" => "Varchar(64)"
	);

	public static $has_one = array(
		"Page" => "SiteTree"
	);

	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $many_many_extraFields = array();

	//database related settings
	static $indexes = array(
		"SessionID" => true,
	);

	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->SessionID = Session_ID();
	}

}
