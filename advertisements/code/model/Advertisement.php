<?php

/* *
 *@author nicolaas[at] sunnysideup.co.nz
 *
 **/

class Advertisement extends DataObject {

	protected static $width = 0;
		static function set_width($v) {self::$width = $v;}
		static function get_width() {return self::$width;}

	protected static $height = 0;
		static function set_height($v) {self::$height = $v;}
		static function get_height() {return self::$height;}

	static function recommended_image_size_statement() {
		$array = array();
		if(self::get_width() ) {
			$array[] = "width = ".self::get_width()."px";
		}
		if(self::get_height() ) {
			$array[] = "height = ".self::get_height()."px";
		}
		$count = count($array);
		if($count == 0) {
			return "No recommeded image size has been set.";
		}
		else {
			return "Recommended size: ".implode(" and ", $array).".";
		}
	}

	static $db = array(
		"Title" => "Varchar(255)",
		"ExternalLink" => "Varchar(150)",
		"Description" => "Text",
		"Sort" => "Int"
	);

	static $has_one = array(
		"AdvertisementImage" => "Image",
		"LinkedPage" => "SiteTree"
	);


	public static $belongs_many_many = array(
		"Parents" => "SiteTree",
	);

	static $casting = array(
		"Link" => "Varchar"
	);

	static $field_labels = array(
	);

	static $defaults = array(
		"Sort" => 1000
	);

	public static $default_sort = "Sort ASC, Title ASC";


	public static $searchable_fields = array("Title" => "PartialMatchFilter");

	public static $singular_name = "Advertisement";
		static function set_singular_name($v) {self::$singular_name = $v;}

	public static $plural_name = "Advertisements";
		static function set_plural_name($v) {self::$plural_name = $v;}

	function getLink() {
		$link = '';
		if($this->ExternalLink) {
			$link = $this->ExternalLink;

		}
		elseif($this->LinkedPageID) {
			if($this->LinkedPage()) {
				$link = $this->LinkedPage()->Link();
			}
		}
		return $link;
	}

	function Link() {
		return $this->getLink();
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeFieldFromTab("Root.Main", "Sort");
		$fields->removeFieldFromTab("Root.Main", "AdvertisementImage");
		$fields->removeFieldFromTab("Root.Main", "AdvertisementImageID");
		$fields->removeFieldFromTab("Root.Main", "LinkedPageID");
		$fields->removeFieldFromTab("Root.Main", "ExternalLink");
		$fields->removeFieldFromTab("Root.Parents", "Parents");
		$fields->removeFieldFromTab("Root", "Parents");
		$fields->addFieldToTab("Root.Main", new ImageField($name = "AdvertisementImage", $title = self::$singular_name." image. ".self::recommended_image_size_statement()));
		if($this->ID) {
			$treeField = new TreeMultiselectField("Parents", _t("Advertisement.GETCMSFIELDSPARENTID", "only show on ... (leave blank to show on all ".self::$singular_name." pages)"), "SiteTree");
			/*$callback = $this->callbackFilterFunctionForMultiSelect();
			if($callback) {
				$treeField->setFilterFunction ($callback);
			}*/
			$fields->addFieldToTab("Root.ShownOn",$treeField);
		}
		$fields->addFieldToTab("Root.OptionalLink", new TextField($name = "ExternalLink", $title = _t("Advertisement.GETCMSFIELDSEXTERNALLINK", "link to external site (e.g. http://www.wikipedia.org) - this will override an internal link")));
		$fields->addFieldToTab("Root.OptionalLink", new TreeDropdownField($name = "LinkedPageID", $title = _t("Advertisement.GETCMSFIELDSEXTERNALLINKID", "link to a page on this website"), $sourceObject = "SiteTree"));
		if(class_exists("DataObjectSorterController")) {
			$fields->addFieldToTab("Root.Position", new LiteralField("AdvertisementsSorter", DataObjectSorterController::popup_link("Advertisement")));
		}
		else {
			$fields->addFieldToTab("Root.Position", new NumericField($name = "Sort", "Sort undex number (the lower the number, the earlier it shows up"));
		}
		$fields->removeFieldFromTab("Root.Main", "AlternativeSortNumber");

		return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if(!$this->Sort) {
			$this->Sort = self::$defaults["Sort"];
		}
	}


	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		DB::query("UPDATE Advertisement SET Title = ID WHERE Title = '' OR Title IS NULL;");
	}


	protected function callbackFilterFunctionForMultiSelect() {
		$inc = AdvertisementDecorator::get_page_classes_with_advertisements();
		$exc = AdvertisementDecorator::get_page_classes_without_advertisements();
		if(is_array($inc) && count($inc)) {
			$string = 'return in_array($obj->class, array(\''.implode("','", $inc).'\'));';
		}
		elseif(is_array($exc) && count($exc)) {
			$string = 'return !in_array($obj->class, array(\''.implode("','", $exc).'\'));';
		}
		if(isset($string)) {
			return create_function('$obj', $string);
		}
		else {
			return false;
		}
	}

	function Image() {
		//will be depreciated in the future.
		return $this->ResizedAdvertisementImage();
	}


	//back-up function...
	function ResizedAdvertisementImage() {

		$resizedImage = null;
		$imageID = intval($this->AdvertisementImageID+ 0);
		if($imageID) {
			$imageObject = DataObject::get_by_id("Image", $imageID);
			if($imageObject) {
				if($imageObject->ID) {
					$imageObject->Title = Convert::raw2att($this->Title);
					$w = Advertisement::get_width();
					$h = Advertisement::get_height();
					if($h && $w) {
						$resizedImage = $imageObject->SetSize($w, $h);
					}
					elseif($h) {
						$resizedImage = $imageObject->SetHeight($h);
					}
					elseif($w) {
						$resizedImage = $imageObject->SetWidth($w);
					}
					else{
						$resizedImage = $imageObject;
					}
				}
				else {
					//debug::show("no image");
				}
			}
			else {
				//debug::show("could not find image");
			}
		}
		else {
			//debug::show("no imageID ($imageID) ");
		}
		return $resizedImage;
	}


}
