<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@package SlideShow
 *@description: this class holds "slides" for the slideshow
 **/

class SlideShowObject extends DataObject {

	public static $db = array(
		"Title" => "Varchar(125)",
		"Description" => "Text"
	);

	public static $has_one = array(
		"Link" => "Page"
	);


	public static $has_one = array(
		"Parent" => "SiteTree",
		"Image" => "Image"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
	 	$fields->removeFieldFromTab("Root.Main", "ParentID");
	 	$fields->removeFieldFromTab("Root.Main", "LinkID");
		$fields->addFieldToTab("Root.Main",new HiddenField("ParentID"));
		$fields->addFieldToTab("Root.Main",new TreeDropdownField("LinkID", "Link - if any - if you would like to create an external link then you can link to a redirect page and make that page redirect to an external link.", "SiteTree"));
		return $fields;
	}

	function fieldLabels() {
		return array(
			'Title' => 'Title',
			'LinkID' => 'Link to page within website'
		);
	}
	public function summaryFields() {
		return $this->fieldLabels();
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}


}
