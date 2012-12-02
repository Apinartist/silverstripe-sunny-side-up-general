<?php


class EcommerceStatisticDataPoint extends DataObject {

	static $db = array(
		"Measure" => "Double",
		"GroupBy" => "Varchar(255)"
	);

	static $has_one = array(
		"EcommerceStatisticBase" => "EcommerceStatisticBase"
	);

	static $indexes = array(
		"Measure" => true,
		"GroupBy" => true
	);

}
