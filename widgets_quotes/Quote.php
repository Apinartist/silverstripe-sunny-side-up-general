<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class Quote extends Widget {

	static $db = array(
		"WidgetTitle" => "Varchar(255)",
		"Quote" => "Varchar(255)",
		"PublishedIn" => "Varchar(255)",
		"ExtraPublishingInformation" => "Varchar(255)",
		"PersonQuoted" => "Varchar(255)",
	);

	static $has_one = array(
		"Photo" => "Image"
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
			new TextField("PersonQuoted", "Person quoted"),
			new ImageField("Photo", "Photo")
		);
	}

	function Title() {
		return $this->WidgetTitle ? $this->WidgetTitle : "";
	}

	function getTitle() {
		return $this->Title;
	}

}

