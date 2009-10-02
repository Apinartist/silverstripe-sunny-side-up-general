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
		$optionArray = array();
		if($source) foreach( $source as $page ) {
			$optionArray[$page->ID] = $page->MenuTitle;
		}
		return new FieldSet(
			new DropdownField("QuickLink1ID","First Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---"),
			new DropdownField("QuickLink2ID","Second Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---"),
			new DropdownField("QuickLink3ID","Third Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---"),
			new DropdownField("QuickLink4ID","Fourth Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---"),
			new DropdownField("QuickLink5ID","Fifth Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---"),
			new DropdownField("QuickLink6ID","Sixth Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---"),
			new DropdownField("QuickLink7ID","Seventh Link",$optionArray, $value = "", $form = null, $emptyString = "--- select page ---")
		);
	}

	function Links() {
		Requirements::themedCSS("quicklinks");
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