<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 */
class WidgetAdvertisement extends Widget {

	static $db = array(
		"Hyperlink" => "Varchar(255)"
	);

	static $has_one = array(
		"Image" => "Image"
	);

	static $has_many = array();

	static $many_many = array();

	static $belongs_many_many = array();

	static $defaults = array();

	static $title = '';

	static $cmsTitle = 'Advertisement';

	static $description = 'Adds a simple image + link (advertisement) to your widget area.';

	function getCMSFields() {
		$source = DataObject::get("SiteTree");
		return new FieldSet(
			new TextField("Hyperlink","Hyperlink (e.g. http://www.sunnysideup.co.nz)"),
			new ImageField("Image","Image / Picture / Logo")
		);
	}


}