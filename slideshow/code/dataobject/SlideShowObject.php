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
		"AutoAdded" => "Boolean"
	);

	public static $has = array(
		"Parent" => "SiteTree",
		"Image" => "Image"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
	 	return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}

	public function summaryFields() {
		return array(
			'Link' => 'Hyperlink (if any) - e.g. http://www.myproduct.com',
			'ParentID' => 'Shown on page...'
			'Parent' => 'Shown on page:'
		);
	}


}