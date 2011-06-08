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

	static $default_order = "\"Rate\" ASC, \"Name\" ASC";

}

