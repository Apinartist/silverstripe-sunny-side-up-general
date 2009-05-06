<?php
/**
 * Add a bunch of things MANY-MANY to your pages
 */


class TemplateOverviewDesignDataObject extends DataObjectDecorator {
	static $db = array(
		"Date" => "Date",
		"BrowserDetails" => "Varchar(255)"
	);
	static $has_one = array(
		"Who" => "Member"
	);
}

