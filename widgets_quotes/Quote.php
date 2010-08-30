<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class Quote extends Widget {

	static $db = array(
		"WidgetTitle" => "Varchar(255)",
		"PublishedIn" => "Varchar(255)",
		"ExtraPublishingInformation" => "Varchar(255)",
		"Quote" => "Varchar(255)",
		"PersonQuoted" => "Varchar(255)",
	);

	static $title = 'Quote';

	static $cmsTitle = 'Quote';

	static $description = 'Allows you to add quote';

	function getCMSFields() {
		return new FieldSet(
			new HeaderField("FieldExplanations", "Enter optional fields below..."),
			new TextField("WidgetTitle", "Title"),
			new TextField("PublishedIn", "Published In"),
			new TextField("PublishedIn", "Published"),
			new TextField("ExtraPublishingInformation", "Extra publishing information, e.g date"),
			new TextField("Quote", "Quote"),
			new TextField("PersonQuoted", "Person quoted")
	}

	function Title() {
		return $this->WidgetTitle ? $this->WidgetTitle : self::$title;
	}

	function getTitle() {
		return $this->Title;
	}

	function RandomDidYouKnowItem() {
		Requirements::themedCSS("widgets_didyouknow");
		$do = DataObject::get_one("DidYouKnow_Statement", null, $cache = true, "RAND() DESC");
		if($do) {
			return $do->Content;
		}
	}

}

class DidYouKnow_Statement extends DataObject {

	static $db = array(
		"Content" => "Varchar(255)"
	);


}
