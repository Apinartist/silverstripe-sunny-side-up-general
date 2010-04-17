<?php

class ImageGalleryPage extends Page {

	//appearance
	static $icon = "mymodule/images/treeicons/ImageGalleryPage-file.gif";

	//parents and children in sitetree
	static $allowed_children = array("ImageGalleryPage"); //can also be "none";

	static $default_child = "ImageGalleryPage";

	//database - use "fieldName" => "FieldClass"
	static $db = array();

	static $has_one = array();

	static $has_many = array(
		"ImageGalleryEntries" => "ImageGalleryEntry"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Images", ImageGalleryEntry::get_has_many_complex_table_field($this, "ImageGalleryEntries"));
		return $fields;
	}


}