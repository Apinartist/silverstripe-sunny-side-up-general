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
		'Title' => 'Varchar(255)',
		"Sort" => "Int"
	);

	public static $has_one = array(
		'Icon' => 'Image',
		'InternalLink' => 'SiteTree'
	);

	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array();

	public static $searchable_fields = array();

	public static $field_labels = array(
		"URL" => "Link (e.g. http://twitter.com/myname/)- will override internal link",
		"Title" => "Title",
		"Sort" => "Sort Index (lower numbers shown first)",
		"Icon" => "Icon",
		"InternalLink" => "Internal Link"
	);

	public static $summary_fields = array(
		"Title" => "Title"
	);

	public static $default_sort = "Sort ASC, Title ASC";

	public static $singular_name = "Social networking link";

	public static $plural_name = "Social networking links";

	function Link() {
		if($this->URL) {
			return $this->URL;
		}
		elseif($this->InternalLinkID) {
			$page = DataObject::get_by_id("SiteTree", $this->InternalLinkID);
			if($page) {
				return $page->Link();
			}
		}
	}


}
