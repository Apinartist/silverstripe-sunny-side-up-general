<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: creates a list of places where people can follow you (e.g. twitter, your blog, etc...)
 *
 **/

class SocialNetworkingLinksDataObject extends DataObject {


	public static $db = array(
		'URL' => 'Varchar(255)',
		'Title' => 'Varchar(255)'
	);

	public static $has_one = array(
		'Icon' => 'Image'
	);

	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array();

	public static $searchable_fields = array();

	public static $field_labels = array(
		"URL" => "location (e.g. http://twitter.com/myname/)",
		"Title" => "Title",
		"Icon" => "Icon (preferably something like 32pixels by 32pixels)"
	);

	public static $summary_fields = array(
		"URL" => "URL",
		"Title" => "Title"
	);

	public static $singular_name = "Social Networking Location";

	public static $plural_name = "Social Networking Locations";



}