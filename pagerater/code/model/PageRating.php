<?php

/**
 *@author nicolaas [at] sunnysideup . co . nz
 *
 **/

class PageRating extends DataObject {

	protected static $stars = array(
		'1' => array("Code" => 'OneStar', "Title" => "One Star"),
		'2' => array("Code" => 'TwoStar', "Title" => "Two Stars"),
		'3' => array("Code" => 'ThreeStar', "Title" => "Three Stars"),
		'4' => array("Code" => 'FourStar', "Title" => "Four Stars"),
		'5' => array("Code" => 'FiveStar', "Title" => "Five Stars")
	);
		static function get_stars() {return self::$stars;}
		static function set_stars($v) {self::$stars = $v;}
		static function get_star_entry_code($value) {
			if(isset(self::$stars[$value]["Code"])) {
				return self::$stars[$value]["Code"];
			}
			return "NA";
		}
		static function get_star_entry_name($value) {
			if(isset(self::$stars[$value]["Title"])) {
				return self::$stars[$value]["Title"];
			}
			return "NA";
		}
		static function get_star_dropdowndown() {
			$array = self::get_stars();
			$newArray = array();
			if(count($array)) {
				foreach($array as $key => $star) {
					$newArray[$key] = $star["Title"];
				}
			}
			return $newArray;
		}
		static function get_number_of_stars() {return count(self::$stars);}

	static $db = array(
 		"Rating" => "Int",
 		"IsDefault" => "Boolean"
	);

	static $has_one = array(
		"Parent" => "Page"
	);

	static $summary_fields = array("Rating", "Parent.Title");

	static $default_sort = "Created DESC";

	static $singular_name = 'Page Rating';

	static $plural_name = 'Page Ratings';

	public static function update_ratings($SiteTreeID = 0) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($SiteTreeID) {
			$where = "{$bt}PageRating{$bt}.{$bt}ParentID{$bt} = ".$SiteTreeID;
		}
		else {
			$where = "{$bt}PageRating{$bt}.{$bt}ParentID{$bt} > 0";
		}
		DB::query("DROP TABLE IF EXISTS PageRating_TEMP;");
		DB::query("CREATE TABLE PageRating_TEMP (ParentID INTEGER(11), Rating INTEGER);");
		DB::query("ALTER TABLE {$bt}PageRating_TEMP{$bt} ADD INDEX ( {$bt}ParentID{$bt} ) ");
		DB::query("ALTER TABLE {$bt}PageRating_TEMP{$bt} ADD INDEX ( {$bt}Rating{$bt} ) ");
		DB::query("
			INSERT INTO {$bt}PageRating_TEMP{$bt}
			SELECT {$bt}ParentID{$bt}, (ROUND(AVG({$bt}PageRating{$bt}.{$bt}Rating{$bt}) * 100))
			FROM {$bt}PageRating{$bt}
			WHERE $where
			GROUP BY {$bt}PageRating{$bt}.{$bt}ParentID{$bt};
			"
		);
		DB::query("
			UPDATE {$bt}SiteTree{$bt}
				INNER JOIN {$bt}PageRating_TEMP{$bt} ON {$bt}SiteTree{$bt}.{$bt}ID{$bt} = {$bt}PageRating_TEMP{$bt}.{$bt}ParentID{$bt}
			SET {$bt}SiteTree{$bt}.{$bt}PageRating{$bt} = ({$bt}PageRating_TEMP{$bt}.{$bt}Rating{$bt} / 100);");
		DB::query("
			UPDATE {$bt}SiteTree_Live{$bt}
				INNER JOIN {$bt}PageRating_TEMP{$bt} ON {$bt}SiteTree_Live{$bt}.{$bt}ID{$bt} = {$bt}PageRating_TEMP{$bt}.{$bt}ParentID{$bt}
			SET {$bt}SiteTree_Live{$bt}.{$bt}PageRating{$bt} = ({$bt}PageRating_TEMP{$bt}.{$bt}Rating{$bt} / 100);");
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if($this->ParentID) {
			self::update_ratings($this->ParentID);
		}
	}

}

