<?php

/**
 *nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class CampaignMonitorList extends DataObject {

	public static $db = array(
		"Title" => "Varchar(255)",
		"CampaignMonitorID" => "Varchar(255)"
	);

	public static $belongs_many_many = array(
		"Members" => "Member"
	);

	public static $searchable_fields = array(
		"Title" => "PartialMatchFilter"
	);
	public static $summary_fields = array("Title" => "Title");

	public static $singular_name = "Subscriber List";

	public static $plural_name = "Subscriber Lists";

	public static $default_sort = "Title ASC";

}
