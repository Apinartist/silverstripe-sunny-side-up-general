<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class Quote extends Widget {

	public static $db = array(
		"WidgetTitle" => "Varchar(255)",
		"SubTitle" => "Varchar(255)",
		"Quote" => "Varchar(255)",
		"PublishedIn" => "Varchar(255)",
		"ExtraPublishingInformation" => "Varchar(255)",
		"PersonQuoted" => "Varchar(255)",
	);

	public static $has_one = array(
		"Photo" => "Image"
	);

	public static $title = 'Quote';

	public static $cmsTitle = 'Quote';

	public static $description = 'Allows you to add quote';

	protected static $folder_name_for_images = 'quote-images';
		static function set_folder_name_for_images($v){self::$folder_name_for_images = $v;}
		static function get_folder_name_for_images(){return self::$folder_name_for_images;}

	function getCMSFields() {
		$folder = Folder::findOrMake(self::get_folder_name_for_images());
		$fields = new FieldSet(
			new HeaderField("FieldExplanations", "Enter optional fields below..."),
			new TextField("WidgetTitle", "Title"),
			new TextField("SubTitle", "Sub title"),
			new TextField("Quote", "Quote"),
			new TextField("PublishedIn", "Published in"),
			new TextField("ExtraPublishingInformation", "Extra publishing information, e.g date"),
			new TextField("PersonQuoted", "Person quoted")
		);
		$hasPhoto = false;
		if($this->ID) {
			$images = DataObject::get("Image", "ParentID = ".$folder->ID);
			if($images) {
				$list = $images->map();
				$fields->push(new DropdownField("PhotoID", "Photo", $list, null, null, " --- select image --- "));
				$hasPhoto = true;
			}
		}
		$fields->push(new HeaderField("PhotoExplanation", '
			<p>HOW TO ADD PHOTO?</p>
			<ul>
				<li>save this page</li>
				<li>make sure you <a href="/admin/assets/show/'.$folder->ID.'">have uploaded</a> the right photo in the following folder: <i>'.self::get_folder_name_for_images().'</i></li>
				<li>come back here and select the photo.</li>
			</ul>',
			$headingLevel = 5,
			$allowHTML = true
		));
		return $fields;
	}

	function Title() {
		return $this->WidgetTitle ? $this->WidgetTitle : "";
	}

	function getTitle() {
		return $this->Title;
	}

	function MyPhoto() {
		return $this->Photo();
	}

}

