<?php

class Affiliation extends DataObject {

	static $db = array(
		"Title" => "Varchar(100)",
		"Code" => "Varchar(100)",
		"HyperLink" => "Varchar(100)"
	);

	static $has_one = array(
		"Parent" => "SiteTree",
		"Logo" => "Image"
	);

	static function get_complex_table_field($controller) {

	}

}