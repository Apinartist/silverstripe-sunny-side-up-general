<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class AdvertisementDecorator extends SiteTreeDecorator {

	protected static $advertisement_dos;

	function Advertisements() {
		if(!self::$advertisement_dos) {
			$doSet = new DataObjectSet();
			$browseSet = $this->AdvertisementsToShow();
			if($browseSet) {
				Requirements::javascript("advertisements/javascript/Advertisement.js");
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
			self::$advertisement_dos = $doSet;
		}
		return self::$advertisement_dos;
	}


	protected function AdvertisementsToShow() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$dos = DataObject::get("Advertisement", "({$bt}Advertisement{$bt}.{$bt}ParentID{$bt} = ".$this->owner->ID." OR {$bt}Advertisement{$bt}.{$bt}ParentID{$bt} IS NULL OR {$bt}Advertisement{$bt}.{$bt}ParentID{$bt} = 0) AND {$bt}Advertisement{$bt}.{$bt}Show{$bt} = 1", "RAND()");
		return $dos;
	}
}
