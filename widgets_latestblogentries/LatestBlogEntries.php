<?php
/**
 * Shows a widget with viewing blog entries
 * by months or years.
 *
 * @package blog
 */
class LatestBlogEntries extends Widget {

	static $db = array(
		'NumberOfItems' => 'Int'
	);
	static $has_one = array();

	static $has_many = array();

	static $many_many = array();

	static $belongs_many_many = array();

	static $defaults = array(
		'NumberOfItems' => 7
	);

	static $title = 'Latest Blog Entries';

	static $cmsTitle = 'Latest Blog Entries';

	static $description = 'Show a list of latest blog entries.';

	function getCMSFields() {
		return new FieldSet(
			new NumericField("NumberOfItems","Number Of Items Shown")
		);
	}

	function Links() {
		Requirements::themedCSS("widgets_latestblogentries");
		return DataObject::get("BlogEntry", null, "`Created` DESC", null, $this->NumberOfItems);
	}

}