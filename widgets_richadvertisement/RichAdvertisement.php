<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class RichAdvertisement extends Widget {

	static $has_one = array(
		"RichAdvertisementWidgetObject" => "RichAdvertisementWidgetObject"
	);

	static $title = 'Rich Advertisement';

	static $cmsTitle = 'Rich Advertisement';

	static $description = 'Allows you to add a richly formatted advertisement';

	function getCMSFields() {
		$options = DataObject::get("RichAdvertisementWidgetObject");
		if($options) {
			return new FieldSet(
				new LiteralField("RichAdvertisementWidgetObjectLink", "Link to modelAdmin goes here..."),
				new DropdownField(
					$name = "RichAdvertisementWidgetObjectID",
					$title = "Select advertisement",
					$source = $options
				)
			);
		}
		else {
			//put some message here on how to add options
		}
	}

	function Title() {
		return $this->WidgetTitle ? $this->WidgetTitle : self::$title;
	}

	function getTitle() {
		return $this->Title;
	}


}
