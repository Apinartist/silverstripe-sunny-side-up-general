<?php

/*
 *@author: nicolaas [at] sunny side up . co . nz
 *
 **/


class SmartChimpNewsletter extends DataObject {

	static $has_one = array(
		"Parent" => "SmartChimpSignupPage"
	);

	static $db = array(
		"CampaignID" => "Varchar(30)",//id
		"Date" => "Datetime",//send_time
		"Title" => "Varchar(255)",//title
		"Subject" => "Varchar(255)",//title
		"PermaLink" => "Varchar(255)",//archive_url
		"WebID" => "Int", //web_id
		"Status" => "Varchar(20)", //should be sent!
		"TextContent" => "Text", //html
		"HTMLContent" => "HTMLText", //text
		"Done" => "Boolean"
	);

	static $indexes = array(
		"CampaignID" => true
	);

	function getDateNice() {
		return $this->obj('Date')->Nice();
	}

	function PermaLink() {
		return str_replace('&amp;', '&', $this->PermaLink);
	}

	function requiredDefaultRecords() {
		parent::requiredDefaultRecords();
	}
	public static $default_sort = "Date DESC";
	public static $defaults = array();//use fieldName => Default Value
	public static $searchable_fields = array("Title" => "PartialMatchFilter");
	public static $field_labels = array("Title" => "Title");
	public static $summary_fields = array("Name" => "Name");
	public static $singular_name = "MailChimp Newsletter";
	public static $plural_name = "MailChimp Newsletters";

	function Title() {
		return $this->Title();
	}

	static function clean_up_characters() {
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'’','\'');");
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'…','');");
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'“','\"');");
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'“','\"');");
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'–','-');");
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'�','\"');");
		DB::query("UPDATE `SmartChimpNewsletter` SET `TextContent` = REPLACE(`TextContent`,'� ','\"');");
	}

}


