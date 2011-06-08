<?php

class Designer extends DataObject {

	public static $db = array(
		"Name" => "Varchar",
		"Location" => "Varchar",
		"Rate" => "Enum('$,$$,$$$,$$$$,$$$$$', '$$$')"
	);

	public static $has_one = array(
		"Sample1" => "Image",
		"Sample2" => "Image",
		"Sample3" => "Image"
	);

	public static $belongs_many_many = array(
		"DesignersPage" => "DesignersPage"
	);

	static $default_order = "\"Rate\" ASC, \"Name\" ASC";

}

