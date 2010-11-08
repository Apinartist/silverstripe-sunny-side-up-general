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
				"UseParentAdvertisements" => "Boolean"
			),
			'has_one' => array(
				"AdvertisementsFolder" => "Folder",
				"AdvertisementStyle" => "AdvertisementStyle"
			),
			'many_many' => array(
				"Advertisements" => "Advertisement"
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

	protected static $specific_name_for_advertisements = "Advertisements";
		static function set_specific_name_for_advertisements($v){self::$specific_name_for_advertisements = $v;}
		static function get_specific_name_for_advertisements(){return self::$specific_name_for_advertisements;}

	protected static $page_classes_with_advertisements = array();
		static function get_page_classes_with_advertisements(){return self::$page_classes_with_advertisements;}
		static function set_page_classes_with_advertisements(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in AdvertisementsDecorator::set_page_classes_with_advertisements()");}
			self::$page_classes_with_advertisements = $array;
		}

	protected static $advertisements_dos;

	public function updateCMSFields(FieldSet &$fields) {
		if($this->classHasAdvertisements($this->owner->ClassName)) {
			$tabName = $this->MyTabName();
			//advertisements shown...
			$advertisements = DataObject::get("Advertisement");
			$fields->addFieldToTab($tabName, $this->MyHeaderField("Actual ".Advertisement::$plural_name." Shown"));
			if($advertisements) {
				$fields->addFieldToTab($tabName, new CheckboxSetField("Advertisements", Advertisement::$plural_name.' to show', $advertisements));
				if(class_exists("DataObjectSorterController")) {
					$fields->addFieldToTab($tabName, new LiteralField("AdvertisementsSorter", DataObjectSorterController::popup_link("Advertisement")));
				}
			}
			else {
				$fields->addFieldToTab($tabName, new LiteralField("AdvertisementsHowToCreate", '<p>Please <a href="/admin/'.AdvertisementAdmin::$url_segment.'/">create '.Advertisement::$plural_name.'</a> on the <i>'.AdvertisementAdmin::$menu_title.'</i> tab first, or see below on how to create '.Advertisement::$plural_name.' from a folder.'));
			}
			if($parent = $this->advertisementParent()) {
				$fields->addFieldToTab($tabName, new CheckboxField("UseParentAdvertisements", "OR  ... use ".Advertisement::$plural_name." from <i>".$parent->Title."</i>"));
			}

			if($styles = DataObject::get("AdvertisementStyle")) {
				$fields->addFieldToTab($tabName, $this->MyHeaderField("Style"));
				$list = $styles->toDropdownMap("ID", "Title",$emptyString = "--select style--", $sortByTitle = true);
				$fields->addFieldToTab($tabName, new DropdownField("AdvertisementStyleID", "Style as created by your developer", $list));
			}


			//use parents
			$fields->addFieldToTab($tabName, $this->MyHeaderField("Create New ".Advertisement::$plural_name));
			$fields->addFieldToTab($tabName, new TreeDropdownField( 'AdvertisementsFolderID', 'Create new '.Advertisement::$plural_name.' from all images in the folder selected below ... (NB '.Advertisement::recommended_image_size_statement().')', 'Folder' ));

			$fields->addFieldToTab($tabName, $this->MyHeaderField("Edit ".Advertisement::$plural_name));
			$fields->addFieldToTab($tabName, new LiteralField("ManageAdvertisements", '<p>Please manage existing <a href="/admin/'.AdvertisementAdmin::$url_segment.'/">'.Advertisement::$plural_name.'</a> on the <i>'.AdvertisementAdmin::$menu_title.'</i> tab.'));

			$fields->addFieldToTab($tabName, $this->MyHeaderField("Delete ".Advertisement::$plural_name));
			$page = DataObject::get_by_id("SiteTree", $this->owner->ID);
			$removeallLink = '/advertisements/removealladvertisements/'.$this->owner->ID.'/';
			$fields->addFieldToTab($tabName, new LiteralField("removealladvertisements",
				'<p><a href="'.$removeallLink.'" onclick="if(confirm(\'Are you sure you want to remove all '.Advertisement::$plural_name.' from this page?\')) {jQuery(\'#removealladvertisements\').load(\''.$removeallLink.'\');} return false;"  id="removealladvertisements">remove all '.Advertisement::$plural_name.' from this page</a>.</p>'
			));
			$deleteallLink = '/advertisements/deletealladvertisements/'.$this->owner->ID.'/';
			$fields->addFieldToTab($tabName, new LiteralField("deletealladvertisements",
				'<p><a href="'.$deleteallLink.'" onclick="if(confirm(\'Are you sure you want to DELETE all '.Advertisement::$plural_name.' from this website?\')) {jQuery(\'#deletealladvertisements\').load(\''.$deleteallLink.'\');} return false;"  id="deletealladvertisements">delete all '.Advertisement::$plural_name.' from this website</a>.</p>'
			));

		}
		return $fields;
	}

	protected function MyTabName() {
		$code = preg_replace("/[^a-zA-Z0-9\s]/", "AAAAAAAA", AdvertisementAdmin::$menu_title);
		$code = str_replace(" ", "", $code);
		return "Root.Content.".$code;
	}

	protected function MyHeaderField($name) {
		$code = preg_replace("/[^a-zA-Z0-9\s]/", "", $name);
		$code = str_replace(" ", "", $code);
		return new LiteralField($code, "<h4 style='margin-top: 20px'>$name</h4>");
	}

	function AdvertisementSet() {
		if(!self::$advertisements_dos) {
			$doSet = new DataObjectSet();
			$browseSet = $this->advertisementsToShow();
			if($browseSet) {
				Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
				Requirements::javascript("advertisements/javascript/Advertisements.js");
				if(self::$use_custom_javascript) {
					$file = project()."/javascript/AdvertisementsExecutive.js";
				}
				elseif($style = $this->owner->AdvertisementStyle()) {
					$file = $style->FileLocation;
				}
				else {
					$file = "advertisements/javascript/AdvertisementsExecutive.js";
				}
				Requirements::javascript($file);
				Requirements::themedCSS("Advertisements");
				foreach($browseSet as $Advertisement) {
					$imageID = intval($Advertisement->AdvertisementImageID+ 0);
					if($imageID) {
						$imageObject = DataObject::get_by_id("Image", $imageID);
						if($imageObject) {
							if($imageObject->ID) {
								$title = Convert::raw2att($imageObject->Title);
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


	protected function advertisementParent() {
		$parent = null;
		if($this->owner->ParentID) {
			$parent = DataObject::get_by_id("SiteTree", $this->owner->ParentID);
		}
		elseif($this->owner->URLSegment != "home") {
			$parent = DataObject::get("SiteTree", "URLSegment = 'home'");
		}
		if($parent) {
			if($this->classHasAdvertisements($parent->ClassName)) {
				return $parent;
			}
		}
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($this->classHasAdvertisements($this->owner->ClassName)) {
			$objects = array(0 => 0);
			$images = array(0 => 0);
			$dos1 = $this->advertisementsToShow();
			if($dos1) {
				foreach($dos1 as $obj) {
					$images[$obj->ID] = $obj->AdvertisementImageID;
					$objects[$obj->ID] = $obj->ID;
				}
			}
			//check for non-existing images and delete advertisements associated with it
			/*
			foreach($images as $objectID => $imageID) {
				if(!DataObject::get_by_id("Image", $imageID)) {
					$obj = DataObject::get_by_id("Advertisement", $objectID);
					if($obj) {
						$obj->delete();
						unset($objects[$objectID]);
					}
				}
			}
			*/
			//check if a folder has been set and create objects
			if($this->owner->AdvertisementsFolderID) {
				$dos2 = DataObject::get("Image", "{$bt}ParentID{$bt} = ".$this->owner->AdvertisementsFolderID." AND {$bt}ID{$bt} NOT IN (".implode(",", $images).")");
				if($dos2) {
					$advertisementsToAdd = array();
					foreach($dos2 as $image) {
						$newAdvertisement = new Advertisement();
						$newAdvertisement->AdvertisementImageID = $image->ID;
						$newAdvertisement->Title = $image->Title;
						$newAdvertisement->AutoAdded = true;
						$newAdvertisement->write();
						$objects[$newAdvertisement->ID] = $newAdvertisement->ID;
					}
					$this->owner->Advertisements()->addMany($objects);
					$this->owner->AdvertisementsFolderID = 0;
				}
			}
			if($this->owner->AdvertisementStyleID) {
				if(!DataObject::get_by_id("AdvertisementStyle",$this->owner->AdvertisementStyleID)) {
					$this->owner->AdvertisementStyleID = 0;
				}
			}
			//remove advdertisements if parent is being used...
			if($this->owner->UseParentAdvertisements) {
				if($this->advertisementParent()) {
					$combos = $this->owner->Advertisements();
					if($combos) {
						$combos->removeAll();
					}
				}
				else {
					$this->owner->UseParentAdvertisements  = false;
				}
			}
		}
	}


	protected function advertisementsToShow() {
		if($this->owner->UseParentAdvertisements) {
			$parent = $this->advertisementParent();
			if($parent) {
				return $parent->advertisementsToShow();
			}
		}
		return $this->owner->Advertisements();
	}

	protected function classHasAdvertisements($className) {
		//assumptions:
		//1. in general YES
		//2. if list of WITH is shown then it must be in that
		//3. otherwise check if it is specifically excluded (WITHOUT)
		$result = true;
		$inc =  self::get_page_classes_with_advertisements();
		$exc =  self::get_page_classes_without_advertisements();
		if(is_array($inc) && count($inc)) {
			$result = false;
			if(in_array($className,$inc)) {
				$result = true;
			}
		}
		elseif(is_array($exc) && count($exc) && in_array($className,$exc))  {
			$result = false;
		}
		return $result;
	}

}

