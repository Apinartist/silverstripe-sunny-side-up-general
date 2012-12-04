<?php
/**
 * Shows a widget with viewing blog entries
 * by months or years.
 *
 * @package blog
 */
class ChildBlogEntries extends Widget {

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

	static $title = 'Child Blog Entries';

	static $cmsTitle = 'Child Blog Entries';

	static $description = 'Show a list of latest child blog entries.';

	function getCMSFields() {
		return new FieldSet(
			new NumericField("NumberOfItems","Number of items shown (will always show newest ones first)")
		);
	}

	function Links() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$widgetAreaID = $this->ParentID;
		$parent = DataObject::get_one("SiteTree", "NewsItemsID = ".$widgetAreaID);
		if($parent) {
			Requirements::themedCSS("widgets_ChildBlogEntries");
			return DataObject::get("BlogEntry", "{$bt}ParentID{$bt} = ".$parent->ID, "{$bt}Created{$bt} DESC", null, $this->NumberOfItems);
		}
	}

}
