<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@package SlideShow
 *@description: this class holds "slides" for the slideshow
 **/

class SlideShowObject extends DataObject {

	public static $db = array(
		"Title" => "Varchar(125)",
		"Link" => "Varchar(125)",
		"Description" => "Text"
	);

	public static $has_one = array(
		"Parent" => "SiteTree",
		"Image" => "Image"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
	 	$fields->removeFieldFromTab("Root.Main", "ParentID");
		$fields->addFieldToTab("Root.Main",new HiddenField("ParentID"));
		return $fields;
	}

	function fieldLabels() {
		return array(
			'Title' => 'Title',
			'Link' => 'Hyperlink (if any) - e.g. http://www.myproduct.com/ or /contact-us'
		);
	}
	public function summaryFields() {
		return $this->fieldLabels();
	}


}
