<?php

class Advertisement extends DataObject {

	protected static $width = 100;
		static function set_width($v) {self::$width = $v;}
		static function get_width() {return self::$width;}

	protected static $height = 100;
		static function set_height($v) {self::$height = $v;}
		static function get_height() {return self::$height;}

	static $db = array(
		"Show" => "Boolean",
		"Title" => "Varchar(255)",
		"ExternalLink" => "Varchar(150)"
	);

	static $has_one = array(
		"AdvertisementImage" => "Image",
		"Parent" => "HomePage",
		"LinkedPage" => "SiteTree"

	);
	static $casting = array(
		"Link" => "Varchar"
	);

	function getLink() {
		if($this->ExternalLink) {
			$link = $this->ExternalLink;
		}
		elseif($this->LinkedPage()) {
			$link = $this->LinkedPage()->Link();
		}
		else {
			$link = '';
		}
		return $link;
	}

	function Link() {
		return $this->getLink();
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeFieldFromTab("Root.Main", "AdvertisementImage");
		$fields->removeFieldFromTab("Root.Main", "AdvertisementImageID");
		$fields->removeFieldFromTab("Root.Main", "LinkedPageID");
		$fields->removeFieldFromTab("Root.Main", "ExternalLink");
		$fields->removeFieldFromTab("Root.Main", "ParentID");
		$fields->addFieldToTab("Root.Main", new ImageField($name = "AdvertisementImage", $title = "advertisement image - width: ".Advertisement::get_width()."px - height: ".Advertisement::get_height()."px "));
		$fields->addFieldToTab("Root.from", new TreeDropdownField($name = "ParentID", $title = "only show on ... (leave blank to show on all advertisement pages)", $sourceObject = "SiteTree"));
		$fields->addFieldToTab("Root.to", new TextField($name = "ExternalLink", $title = "link to external site (e.g. http://www.wikipedia.org) - this will override an internal link"));
		$fields->addFieldToTab("Root.to", new TreeDropdownField($name = "LinkedPageID", $title = "link to a page on this website", $sourceObject = "SiteTree"));
	 	return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}

	static $field_labels = array(
		'Show' => 'Show: 1 = show, 0 = hide'
	);

	public static $searchable_fields = array("Title" => "PartialMatchFilter");
	public static $singular_name = "Advertisement";
	public static $plural_name = "Advertisements";

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		DB::query("UPDATE Advertisement SET Title = ID WHERE Title = '' OR Title IS NULL;");
	}


}
