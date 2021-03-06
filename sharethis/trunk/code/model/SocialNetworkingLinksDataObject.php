<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: creates a list of places where people can follow you (e.g. twitter, your blog, etc...)
 *
 */
class SocialNetworkingLinksDataObject extends DataObject {

	static $db = array(
		'URL' => 'Varchar(255)',
		'Title' => 'Varchar(255)',
		'Sort' => 'Int'
	);

	static $casting = array(
		'Code' => 'Varchar(255)',
		'Link' => 'Varchar(255)',
		'IconHTML' => 'HTMLText'
	);

	static $has_one = array(
		'Icon' => 'Image',
		'InternalLink' => 'Page'
	);

	static $searchable_fields = array(
		'Title' => 'PartialMatchFilter'
	);

	static $field_labels = array(
		'URL' => 'Link (e.g. http://twitter.com/myname/)- will override internal link',
		'InternalLink' => 'Internal Link',
		'Title' => 'Title',
		'Sort' => 'Sort Index (lower numbers shown first)',
		'IconID' => 'Icon (preferably something like 32pixels by 32pixels)'
	);

	static $summary_fields = array(
		'Title' => 'Title',
		'IconHTML' => 'Icon'
	);

	static $default_sort = 'Sort ASC, Title ASC';

	static $singular_name = 'Social networking link';

	static $plural_name = 'Social networking links';

	/**
	 * @return String - returns the title with all non-alphanumeric + spaces removed.
	 */
	function Code() {
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $this->Title));
	}

	function getIconHTML() {
		$icon = $this->Icon();
		if($icon->exists()) {
			return $icon->SetHeight(32);
		}
		return '<img src="/' . SS_SHARETHIS_DIR . "/images/icons/{$this->Code}.png\" alt=\"{$this->Code}\"/>";
	}

	function Link() {
		if($this->URL) {
			return $this->URL;
		}
		elseif($this->InternalLinkID) {
			$page = SiteTree::get()->byID($this->InternalLinkID);
			if($page->exists()) {
				return $page->Link();
			}
		}
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		if($this->ID) {
			$fields->addFieldToTab('Root.Main', new LiteralField('Code', "<p>Code: {$this->Code()}</p>"));
			$fields->addFieldToTab('Root.Main', new LiteralField('Link', "<p>Link: <a href=\"{$this->Link()}\">{$this->Link()}</a></p>"));
			$fields->addFieldToTab('Root.Main', new LiteralField('Link', "<p>{$this->IconHTML()}</p>"));
		}
		$fields->removeFieldFromTab('Root.Main', 'InternalLinkID');
		$fields->addFieldToTab('Root.Main', new TreeDropdownField('InternalLinkID', 'Internal Link', 'SiteTree'), 'URL');
		return $fields;
	}
}
