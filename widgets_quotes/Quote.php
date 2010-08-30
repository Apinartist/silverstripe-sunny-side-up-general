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
		$fields = new FieldSet(
			new HeaderField("FieldExplanations", "Enter optional fields below..."),
			new TextField("WidgetTitle", "Title"),
			new TextField("Quote", "Quote"),
			new TextField("PublishedIn", "Published In"),
			new TextField("ExtraPublishingInformation", "Extra publishing information, e.g date"),
			new TextField("PersonQuoted", "Person quoted")
		);
		$hasPhoto = false;
		if($this->ID) {
			$images = DataObject::get("Image");
			if($images) {
				$list = $images->map();
				$fields->push(new DropdownField("PhotoID", "Photo", $list, null, null, " --- select image --- "));
				$hasPhoto = true;
			}
		}
		if(!$hasPhoto) {
			$fields->push(new LiteralField("PhotoExplanation", '
				<p>NOTE: </p>
				<ul>
					<li>save this page</li>
					<li>make sure you <a href="/admin/assets/">have uploaded</a> a photo</li>
					<li>come back here and select a photo of the person quoted.</li>
				</ul>'));
		}
		else {
			$fields->push(new LiteralField("PhotoExplanation", 'test'));
		}
		return $fields;
	}

	function Title() {
		return $this->WidgetTitle ? $this->WidgetTitle : "";
	}

	function getTitle() {
		return $this->Title;
	}

}

