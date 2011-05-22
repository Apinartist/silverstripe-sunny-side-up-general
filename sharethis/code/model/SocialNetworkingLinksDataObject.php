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

	public static $casting = array(
		'Code' => 'Varchar(255)'
	);

	public static $has_one = array(
		'Icon' => 'Image',
		'InternalLink' => 'SiteTree'
	);

	public static $defaults = array();

	public static $searchable_fields = array(
		"Title" => "PartialMatchFilter"
	);

	public static $field_labels = array(
		"URL" => "Link (e.g. http://twitter.com/myname/)- will override internal link",
		"Title" => "Title",
		"Sort" => "Sort Index (lower numbers shown first)",
		"IconID" => "Icon (preferably something like 32pixels by 32pixels)",
		"InternalLink" => "Internal Link"
	);

	public static $summary_fields = array(
		"Title" => "Title"
	);

	public static $default_sort = "\"Sort\" ASC, \"Title\" ASC";

	public static $singular_name = "Social networking link";

	public static $plural_name = "Social networking links";

	/**
	 *
	 *@return String - returns the title with all non-alphanumeric + spaces removed.
	 **/
	function Code() {
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $this->Title));
	}

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
