<?php

/* *
 *@author nicolaas[at] sunnysideup.co.nz
 *
 **/

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
		"ExternalLink" => "Varchar(150)",
		"Description" => "Text"
	);

	static $has_one = array(
		"AdvertisementImage" => "Image",
		"LinkedPage" => "SiteTree"
	);


	public static $belongs_many_many = array(
		"Parent" => "SiteTree",
	);

	static $casting = array(
		"Link" => "Varchar"
	);

	static $field_labels = array(
		'Show' => 'Show: 1 = show, 0 = hide'
	);

	public static $searchable_fields = array("Title" => "PartialMatchFilter");

	public static $singular_name = "Advertisement";

	public static $plural_name = "Advertisements";


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
		$fields->addFieldToTab("Root.Main", new ImageField($name = "AdvertisementImage", $title = _t("Advertisement.GETCMSFIELDSSIZEINFOWIDTH", "advertisement image - width: ").Advertisement::get_width()._t("Advertisement.GETCMSFIELDSSIZEINFOHEIGHT", "px - height: ").Advertisement::get_height()._t("Advertisement.GETCMSFIELDSSIZEINFOPX", "px ")));
		$fields->addFieldToTab("Root.Main",new HiddenField("ParentID"));
		$treeField = new TreeMultiselectField("Parent", _t("Advertisement.GETCMSFIELDSPARENTID", "only show on ... (leave blank to show on all advertisement pages)"), "SiteTree");
		$callback = $this->callbackFilterFunctionForMultiSelect();
		if($callback) {
			$treeField->setFilterFunction ($callback);
		}
		$fields->addFieldToTab("Root.Main",$treeField);
		$fields->addFieldToTab("Root.to", new TextField($name = "ExternalLink", $title = _t("Advertisement.GETCMSFIELDSEXTERNALLINK", "link to external site (e.g. http://www.wikipedia.org) - this will override an internal link")));
		$fields->addFieldToTab("Root.to", new TreeDropdownField($name = "LinkedPageID", $title = _t("Advertisement.GETCMSFIELDSEXTERNALLINKID", "link to a page on this website"), $sourceObject = "SiteTree"));
	 	return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}


	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		DB::query("UPDATE Advertisement SET Title = ID WHERE Title = '' OR Title IS NULL;");
	}


	protected function callbackFilterFunctionForMultiSelect() {
		$inc = AdvertisementDecorator::get_page_classes_with_advertisements();
		$exc = AdvertisementDecorator::get_page_classes_without_advertisements();
		if(is_array($inc) && count($inc)) {
			$string = 'return in_array($obj->class, array(\''.implode("','", $inc).'\'));';
		}
		elseif(is_array($exc) && count($exc)) {
			$string = 'return !in_array($obj->class, array(\''.implode("','", $exc).'\'));';
		}
		if($string) {
			return create_function('$obj', $string);
		}
		else {
			return false;
		}
	}




}
