<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 */
class QuickLinks extends Widget {

	static $db = array();

	static $has_one = array(
		"QuickLink1" => "SiteTree",
		"QuickLink2" => "SiteTree",
		"QuickLink3" => "SiteTree",
		"QuickLink4" => "SiteTree",
		"QuickLink5" => "SiteTree",
		"QuickLink6" => "SiteTree",
		"QuickLink7" => "SiteTree"
	);

	static $has_many = array();

	static $many_many = array();

	static $belongs_many_many = array();

	static $defaults = array();

	static $title = 'Quick Links';

	static $cmsTitle = 'Quick Links';

	static $description = 'Adds a customisable list of links.';

	function getCMSFields() {
		$source = DataObject::get("SiteTree");
		$optionArray = $source->toDropDownMap($index = 'ID', $titleField = 'Title', "--- select page ---", $sort = "Sort");
		if($source) foreach( $source as $page ) {
			$optionArray[$page->ID] = $page->MenuTitle;
		}
		return new FieldSet(
			new DropdownField("QuickLink1ID","First Link",$optionArray),
			new DropdownField("QuickLink2ID","Second Link",$optionArray),
			new DropdownField("QuickLink3ID","Third Link",$optionArray),
			new DropdownField("QuickLink4ID","Fourth Link",$optionArray),
			new DropdownField("QuickLink5ID","Fifth Link",$optionArray),
			new DropdownField("QuickLink6ID","Sixth Link",$optionArray),
			new DropdownField("QuickLink7ID","Seventh Link",$optionArray)
		);
	}

	function Links() {
		Requirements::themedCSS("widgets_quicklinks");
		$dos = new DataObjectSet();
		for($i = 1; $i < 8; $i++) {
			$fieldname = "QuickLink".$i."ID";
			if($this->$fieldname > 0) {
				if($page = DataObject::get_by_id("SiteTree", $this->$fieldname - 0)) {
					$dos->push($page);
				}
			}
		}
		return $dos;
	}

}