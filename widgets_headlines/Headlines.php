<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class Headlines extends Widget {

	static $db = array(
		"NumberOfHeadlinesShown" => "Int"
	);

	static $defaults = array(
		"NumberOfHeadlinesShown" => 5
	);

	protected static $boolean_field_used_to_identify_headline = "";
		static function set_boolean_field_used_to_identify_headline($v) {self::$boolean_field_used_to_identify_headline = $v;}

	static $title = 'Headlines';

	static $cmsTitle = 'Headlines';

	static $description = 'Adds a list of identified headlines';

	function getCMSFields() {
		return new FieldSet(
			new NumericField("NumberOfHeadlinesShown","Number of Headlines Shown")
		);
	}

	function Links() {
		Requirements::themedCSS("widgets_headlines");
		$where = null;
		if(self::$boolean_field_used_to_identify_headline) {
			$where = "`".self::$boolean_field_used_to_identify_headline."` = 1";
		}
		$dos = DataObject::get("BlogEntry",$where, "`Date` DESC", null, "0, ".$this->NumberOfHeadlinesShown);
		return $dos;
	}

}