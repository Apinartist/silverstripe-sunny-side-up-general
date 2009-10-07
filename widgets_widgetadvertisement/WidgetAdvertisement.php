<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 */
class WidgetAdvertisement extends Widget {

	static $db = array(
		"Hyperlink" => "Varchar(255)"
	);

	static $has_one = array(
		"Logo" => "Image"
	);

	static $has_many = array();

	static $many_many = array();

	static $belongs_many_many = array();

	static $defaults = array();

	static $title = '';

	static $cmsTitle = 'Advertisement';

	static $description = 'Adds a simple image + link (advertisement) to your widget area.';

	function getCMSFields() {
		$dataSet = DataObject::get("File", '`ClassName` = "Image"', "`Title`");
		$dropDownSource = $dataSet->toDropDownMap($index = 'ID', $titleField = 'Title', "--- select file from files and images ---");
		$fields = new FieldSet();
		$fields->push(new TextField("Hyperlink","Hyperlink (e.g. http://www.sunnysideup.co.nz)"));
		$fields->push(new HeaderField("MakeSureToUploadImageFirst","Please make sure image is uploaded first in file and images section", 5));
		$fields->push(new DropdownField("LogoID","Image / Picture / Logo", $dropDownSource));
		return $fields;
	}

	function Data() {
		$dos = new DataObjectSet();
		$dos->Hello = "yyyy";
		$dos->Hyperlink = $this->Hyperlink;
		$dos->Logo = $this->Logo();
		return $dos;
	}

}