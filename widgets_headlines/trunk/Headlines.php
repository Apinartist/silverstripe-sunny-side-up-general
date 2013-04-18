<?php

/**
 * @author nicolaas [at] sunnysideup.co.nz
 **/
class Headlines extends Widget {

	static $db = array(
		'NumberOfHeadlinesShown' => 'Int'
	);

	static $defaults = array(
		'NumberOfHeadlinesShown' => 5
	);

	protected static $boolean_field_used_to_identify_headline = '';
		static function set_boolean_field_used_to_identify_headline($v) {self::$boolean_field_used_to_identify_headline = $v;}

	static $title = 'Headlines';

	static $cmsTitle = 'Headlines';

	static $description = 'Adds a list of identified headlines';

	function getCMSFields() {
		return new FieldList(
			new NumericField('NumberOfHeadlinesShown', 'Number of Headlines Shown')
		);
	}

	function Links() {
		Requirements::themedCSS('widgets_headlines');
		$entries = BlogEntry::get()->sort('Date', 'DESC')->limit($this->NumberOfHeadlinesShown);
		if(self::$boolean_field_used_to_identify_headline) {
			$entries = $entries->filter(self::$boolean_field_used_to_identify_headline, 1);
		}
		return $entries;
	}
}
