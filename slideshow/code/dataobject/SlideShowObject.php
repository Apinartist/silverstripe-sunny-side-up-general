<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@package SlideShow
 *@description: this class holds "slides" for the slideshow
 **/

class SlideShowObject extends DataObject {

	public static $db = array(
		"Link" => "Varchar(125)",
		"Title" => "Varchar(125)",
		"Description" => "Text"
	);

	public static $has_one = array(
		"Parent" => "SiteTree",
		"Image" => "Image"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
	 	$fields->removeFieldFromTab("Root.Main", "Parent");
	 	$fields->removeFieldFromTab("Root.Main", "ParentID");
	 	$fields->removeFieldFromTab("Root.Main", "Image");
		return $fields;
	}

	public function summaryFields() {
		return array(
			'Title' => 'image title',
			'Link' => 'Hyperlink (if any) - e.g. http://www.myproduct.com/ or /contact-us',
			'ParentID' => 'Shown on page...',
			'Parent' => 'Shown on page:'
		);
	}


}
