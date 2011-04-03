<?php

class TemplateOverviewBug extends DataObject {

	protected static $error_email = "";
		static function set_error_email($s) {self::$error_email = $s;}
		static function get_error_email() {return self::$error_email;}

	static $db = array(
		"Title" => "Varchar(255)",
		"WhatWasExpected" => "Text",
		"WhatActuallyHappened" => "Text",
		"OtherInformation" => "Text",
		"OutcomeNote" => "Text",
		"FixedBy" => "Varchar",
		"Fixed" => "Boolean"
	);

	static $has_one = array(
		"Screenshot1" => "Image",
		"Screenshot2" => "Image",
		"Screenshot3" => "Image",
		"Member" => "Member",
		"Template" => "TemplateOverviewDescription",
		"Page" => "SiteTree"
	);

	public static $searchable_fields = array(
		"Title" => "PartialMatchFilter",
		"Fixed",
	);
	public static $field_labels = array(
		"Title" => "Short Description (e.g. newsletter page does not open)",
		"Member" => "Reported by",
	);

	public static $singular_name = "Bug report";

	public static $plural_name = "Bug reports";
	//CRUD settings
	public static $default_sort = "Fixed, LastEdited DESC, Created DESC";



	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->MemberID = Member::currentUserID();
	}

	function onAfterWrite() {
		$email = new Email($from = Email::$admin_email_address, $to = self::get_error_email(), $subject = "new bug on ".Director::absoluteBaseURL(), $body = "new bug on ".Director::absoluteBaseURL());
		$email->send();
	}


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Screenshot1ID");
		$fields->removeByName("Screenshot1ID");
		$fields->removeByName("Screenshot3ID");
		$fields->removeByName("PageID");
		$fields->removeByName("TemplateID");
		$fields->removeByName("MemberID");
		$fields->removeByName("Fixed");
		$fields->removeByName("FixedBy");
		$fields->removeByName("OutcomeNote");
		$fields->addFieldToTab("Root.RelatesTo", new TreeDropdownField("PageID", "Relevant page (if any)", "SiteTree"));
		$templates = DataObject::get("TemplateOverviewDescription");
		if($templates) {
			$fields->addFieldToTab("Root.RelatesTo", new DropdownField("TemplateID", "Relevant page type (if any)", $templates->toDropdownMap('ID','ClassNameLinkFancy', " --- please select ---")));
		}

		$fields->addFieldToTab("Root.ScreenShots", new LiteralField("HowToMakeAScreenShot", '<h3>Learn how to make <a target="_blank" href="http://www.google.com.au/search?&q=How+To+Make+ScreenShot">a screenshot</a></h3>'));
		$fields->addFieldToTab("Root.ScreenShots", new ImageField("Screenshot1", "Required First screenshot"));
		$fields->addFieldToTab("Root.ScreenShots", new ImageField("Screenshot2", "Second screenshot (optional)"));
		$fields->addFieldToTab("Root.ScreenShots", new ImageField("Screenshot3", "Third screenshot (optional)"));
		$fields->addFieldToTab("Root.Outcome", new CheckboxField("Fixed", "Fixed"));
		$fields->addFieldToTab("Root.Outcome", new TextField("FixedBy", "Fixed by ..."));
		$fields->addFieldToTab("Root.Outcome", new TextareaField("OutcomeNote", "Notes about fix..."));
		return $fields;
	}


}
