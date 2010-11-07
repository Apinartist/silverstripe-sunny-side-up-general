<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class AdvertisementDecorator extends SiteTreeDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				"UseParentSlides" => "Boolean",
				"AdvertisementsIDList" => "Text"
			),
			'has_one' => array(
				"AdvertisementsFolder" => "Folder"
				"AdvertisementStyle" => "AdvertisementStyle"
			),
			'many_many' => array(
				"Advertisement" => "Advertisement"
			)
		);
	}

	protected static $use_custom_javascript = false;
		static function set_use_custom_javascript($v){self::$use_custom_javascript = $v;}

	protected static $page_classes_without_advertisements = array();
		static function get_page_classes_without_advertisements(){return self::$page_classes_without_advertisements;}
		static function set_page_classes_without_advertisements(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in AdvertisementsDecorator::set_page_classes_without_advertisements()");}
			self::$page_classes_without_advertisements = $array;
		}

	protected static $page_classes_with_advertisements = array();
		static function get_page_classes_with_advertisements(){return self::$page_classes_with_advertisements;}
		static function set_page_classes_with_advertisements(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in AdvertisementsDecorator::set_page_classes_with_advertisements()");}
			self::$page_classes_with_advertisements = $array;
		}

	protected static $advertisements_dos;


	function Advertisements() {
		if(!self::$advertisements_dos) {
			$doSet = new DataObjectSet();
			$browseSet = $this->advertisementsToShow();
			if($browseSet) {
				Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
				Requirements::javascript("advertisements/javascript/Advertisement.js");
				if(self::$use_custom_javascript) {
					$folder = project()."/javascript/AdvertisementsExecutive.js";
				}
				elseif($style = $this->owner->AdvertisementStyle()) {
					$file = $style->FileLocation;
				}
				else {
					$file = "advertisements/javascript/AdvertisementsExecutive.js"
				}
				Requirements::javascript($folder);
				Requirements::themedCSS("Advertisements");
				foreach($browseSet as $Advertisement) {
					$imageID = intval($Advertisement->AdvertisementImageID+ 0);
					if($imageID) {
						$imageObject = DataObject::get_by_id("Image", $imageID);
						if($imageObject) {
							if($imageObject->ID) {
								$fileName = Convert::raw2js($imageObject->Filename);
								$title = Convert::raw2js($imageObject->Title);
								$resizedImage = $imageObject->SetWidth(Advertisement::get_width());
								if($resizedImage) {
									$record = array(
										'Image' => $resizedImage,
										'Title' => $title,
										'Link' => $Advertisement->Link()
									);
									$doSet->push(new ArrayData($record));
								}
								else {
									//debug::show("no resized image");
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
				}
			}
			self::$advertisements_dos = $doSet;
		}
		return self::$advertisements_dos;
	}


	public function updateCMSFields(FieldSet &$fields) {
		if($this->classHasAdvertisements($this->owner->ClassName)) {
			$fields->addFieldToTab('Root.Content.SlideShowImages', new HeaderField("ImageExplanation", "Image Selection Tools - the area for images is ".Advertisement::get_width()."px wide by ".Advertisement::get_height()."px high" , 3));
			$folder = new TreeDropdownField( 'AdvertisementsFolderID', 'Show ALL images From - you can leave this blank', 'Folder' );
			$fields->addFieldToTab('Root.Content.Advertisements', $folder);
			if($this->owner->ParentID) {
				$fields->addFieldToTab('Root.Content.Advertisements', new CheckboxField("UseParentSlides", "Use parent slides when this page has no slides itself"));
			}
			elseif($this->owner->URLSegment != "home") {
				$fields->addFieldToTab('Root.Content.Advertisements', new CheckboxField("UseParentSlides", "Use homepage slides IF this page has no slides and the homepage does"));
			}
			$tableField = $this->advertisementsTableField();
			$slides = DataObject::get("Advertisement");
			if($slides) {
				$fields->addFieldToTab("Root.Content.Advertisements", new CheckboxSetField("Advertisement", 'Slides to show', $slides));
			}
			$fields->addFieldToTab("Root.Content.Advertisements", new LiteralField("ManageSlideItems", '<p>Please manage <a href="/admin/Advertisements/">slide show items</a> here.'));
			$page = DataObject::get_by_id("SiteTree", $this->owner->ID);
			$removeallLink = '/advertisements/removealladvertisements/'.$this->owner->ID.'/';
			$fields->addFieldToTab('Root.Content.SlideShowImages', new LiteralField("removeallslides",
				'<p><a href="'.$removeallLink.'" onclick="jQuery(\'#removeallslides\').load(\''.$removeallLink.'\'); return false;"  id="removeallslides">remove all slides from this page</a></p>'
			));

		}
		return $fields;
	}



	protected function advertisementsTableField() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$tablefield = new HasManyComplexTableField(
			$controller = $this->owner,
			$name = 'Advertisement',
			$sourceClass = 'Advertisement',
			$fieldList =  array("Link" => "Link", "Title" => "Title")
		);
		$tablefield->setAddTitle("select images for Advertisements");
		$tablefield->setPageSize(100);
		$tablefield->setPermissions(array("edit", "delete", "add"));
		return $tablefield;
	}




	public function onBeforeWrite() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($this->classHasAdvertisements($this->owner->ClassName)) {
			$objects = array(0 => 0);
			$images = array(0 => 0);
			$dos1 = $this->owner->Advertisement();
			if($dos1) {
				foreach($dos1 as $obj) {
					$images[$obj->ID] = $obj->ImageID;
					$objects[$obj->ID] = $obj->ID;
				}
			}
			foreach($images as $objectID => $imageID) {
				if(!DataObject::get_by_id("Image", $imageID)) {
					$obj = DataObject::get_by_id("Advertisement", $objectID);
					if($obj) {
						$obj->delete();
						unset($objects[$objectID]);
					}
				}
			}
			if($this->owner->AdvertisementsFolderID) {
				$dos2 = DataObject::get("Image", "{$bt}ParentID{$bt} = ".$this->owner->AdvertisementsFolderID." AND {$bt}ID{$bt} NOT IN (".implode(",", $images).")");
				if($dos2) {
					foreach($dos2 as $obj) {
						$item = new Advertisement();
						$item->ImageID = $obj->ID;
						$item->Title = $obj->Title;
						$item->ParentID = $parentID;
						$item->AutoAdded = true;
						$item->write();
						$objects[$item->ID] = $item->ID;
					}
					$this->owner->AdvertisementsFolderID = 0;
				}
				$dos1 = DataObject::get("Advertisement", "{$bt}Advertisement{$bt}.{$bt}ID{$bt} IN (".implode(",", $objects).")", "RAND()");
			}
			if(!$dos1 && $this->owner->UseParentSlides) {
				$parent = DataObject::get_by_id("SiteTree", $parentID);
				if($parent) {
					$this->owner->AdvertisementsIDList = $parent->AdvertisementsIDList;
				}
				elseif($this->URLSegment != "home") {
					$home = DataObject::get_one("SiteTree", "{$bt}URLSegment{$bt} = 'home'");
					if($home) {
						$this->owner->AdvertisementsIDList = $home->AdvertisementsIDList;
					}
				}
			}
			elseif($dos1) {
				$this->owner->AdvertisementsIDList = implode(",",$dos1->column("ID"));
			}
		}
		parent::onBeforeWrite();
	}


	protected function advertisementsToShow() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($this->owner->AdvertisementsIDList) {
			return DataObject::get("Advertisement", "{$bt}ID{$bt} IN (".$this->owner->AdvertisementsIDList.")");
		}
	}

	protected function classHasAdvertisements($className) {
		//assumptions:
		//1. in general YES
		//2. if list of WITH is shown then it must be in that
		//3. otherwise check if it is specifically excluded (WITHOUT)
		$result = true;
		if(is_array(self::get_page_classes_with_advertisements()) && count(self::get_page_classes_with_advertisements())) {
			$result = false;
			if(in_array($className,self::get_page_classes_with_advertisements())) {
				$result = true;
			}
		}
		elseif(is_array(self::get_page_classes_without_advertisements()) && in_array($className,self::get_page_classes_without_advertisements()))  {
			$result = false;
		}
		return $result;
	}

}

