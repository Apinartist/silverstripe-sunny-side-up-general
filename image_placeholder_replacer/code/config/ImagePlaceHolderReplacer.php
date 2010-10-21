<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz + toro[at] sunnysideup.co.nz
 *
 *
 **/

class ImagePlaceHolderReplacer extends DataObjectDecorator {

	//Notes e.g. "this image is shown on the homepage under products" - optional
	//CopyFromPath  -e .g. themes/mytheme/images/myImage.gif - optional
	proctected static $images_to_replace = array();
		public static function get_images_to_replace() {return self::$images_to_replace;}
		public static function remove_image_to_replace($className, $fieldName) {unset(self::$images_to_replace[$className.'_'.$fieldName]);}
		public static function add_image_to_replace($className, $fieldName, $notes, $copyFrom) {
			$key = $className.'_'.$fieldName;
			self::$image_to_replace[$key] = array(
				"ClassName" => $className,
				"FieldName" => $fieldName,
				"Notes" => $notes,
				"CopyFromPath" => $notes,
				"DBFieldName" => $key
			);
		}
	proctected static $folder_name = "ImagePlaceHolderSampleImages";
		public static function get_folder_name() {return self::$folder_name;}
		public static function set_folder_name($v) {self::$folder_name = $v;}


	function extraStatics(){
		$hasOneArray = array();
		$fullArray = self::get_images_to_replace();
		if($fullArray) {
			foreach( as $key => $array) {
				$hasOneArray[$key] = "Image";
			}
			return array('has_one' => $hasOneArray);
		}
		return array();
	}

	function updateCMSFields(FieldSet &$fields) {
		$fullArray = self::get_images_to_replace();
		if($fullArray) {
			$folder = Folder::findOrMake(self::get_folder_name());
			if(!$folder) {
				$fields->addFieldToTab('Root.Main', new LiteralField('folderError'.self::get_folder_name(), '<p>Can not create folder for place holder images(/assets/'.self::get_folder_name().'), please contact your administrator.</p>'));
			}
			foreach($fullArray as $key => $array) {
				$fields->addFieldToTab('Root.Main', new ImageField($key, "Place holder for $key ($notes)", $value = null, $form = null, $rightTitle = null, $folder->Name));
			}
		}
		return $fields;
	}
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$bt = defined('DB::USE_ANSI_SQL') ? "'" : '`';

		$update = array();
		$siteConfig = DataObject::get_one('SiteConfig');
		$folder = Folder::findOrMake(self::get_folder_name());
		if($siteConfig) {
			$fullArray = self::get_images_to_replace();
			//copying ....
			if($fullArray) {
				foreach($fullArray as $key => $array) {
					$dbFieldName = $array["DBFieldName"];
					$fileName = basename($array["CopyFromPath"]);
					$fromLocation = Director::baseFolder().'/'.$array["CopyFromPath"];
					$toLocationShort = "assets/".self::get_folder_name()."/{$fileName}";
					$toLocationLong = Director::baseFolder().'/'.$toLocationShort;
					$image = DataObject::get_one('Image', "Filename={$bt}$toLocation{$bt}");
					if(!$image){
						copy($fromLocation, $toLocationLong);
						$image = new Image();
						$image->setName($fileName);
						$image->write();
					}
					if(!$siteConfig->$dbFieldName) {
						$siteConfig->$dbFieldName = $image->ID;
						$update[]= "created placeholder image for $key";
					}
					if($image && $image->ID) {
						DB::query("
							UPDATE {$bt}".$array["ClassName"]."{$bt}
							SET {$bt}".$array["FieldName"]."{$bt} = ".$image->ID."
							WHERE {$bt}".$array["FieldName"]."{$bt} IS NULL
							OR {$bt}".$array["FieldName"]."{$bt} = 0
						");
						if($versioned) {
							DB::query("
								UPDATE {$bt}".$array["ClassName"]."_Live{$bt}
								SET {$bt}".$array["FieldName"]."{$bt} = ".$image->ID."
								WHERE {$bt}".$array["FieldName"]."{$bt} IS NULL
								OR {$bt}".$array["FieldName"]."{$bt} = 0
							");
						}
					}


					if(!$siteConfig->Title) {
						$siteConfig->Title = 'Voiceplus';
						$update[]= "created default entry for Title";
					}
					if(!$siteConfig->CallUs) {
						$siteConfig->CallUs = 'Call us on 1300 887 767';
						$update[]= "created default entry for CallUs";
					}
					if(!$siteConfig->CustomerLoginURL) {
						$siteConfig->CustomerLoginURL = $this->CustomerLoginURLDefault();
						$update[]= "created default entry for CustomerLoginURL";
					}
					if(!$siteConfig->SiteLogoID) {
						$logo = 'Logo.png';
						$image = DataObject::get_one('Image', "Filename={$bt}assets/{$logo}{$bt}");
						if($image){
							$siteConfig->SiteLogoID = $image->ID;
						}
						else{
							copy(Director::baseFolder().'/themes/main/images/'.$logo, Director::baseFolder().'/assets/'.$logo);
							$image = new Image();
							$image->setName($logo);
							$image->write();
							$siteConfig->SiteLogoID = $image->ID;
						}
						$update[]= "created default entry for SiteLogo ".$image->ID;
					}
					if(!$siteConfig->FooterMenuLabel1) {
						$siteConfig->FooterMenuLabel1 = 'Learn About Us';
						$update[]= 'created default entry for FooterMenuLabel1';
					}
					if(!$siteConfig->FooterMenuLabel2) {
						$siteConfig->FooterMenuLabel2 = 'Get Help';
						$update[]= 'created default entry for FooterMenuLabel2';
					}
					if(!$siteConfig->FooterMenuLabel3) {
						$siteConfig->FooterMenuLabel3 = 'Do More';
						$update[]= 'created default entry for FooterMenuLabel3';
					}
					if(!$siteConfig->FooterMenuLabel4) {
						$siteConfig->FooterMenuLabel4 = 'Handsets';
						$update[]= 'created default entry for FooterMenuLabel4';
					}
					if(!$siteConfig->FooterMenuLabel5) {
						$siteConfig->FooterMenuLabel5 = 'Routers & Modems';
						$update[]= 'created default entry for FooterMenuLabel5';
					}
					if(!$siteConfig->FooterSocialLinksLabel) {
						$siteConfig->FooterSocialLinksLabel = 'Socialise';
						$update[]= 'created default entry for FooterSocialLinksLabel';
					}
					if(!$siteConfig->FooterRequestQuoteLabel) {
						$siteConfig->FooterRequestQuoteLabel = 'Need help? Request a free quote.';
						$update[]= 'created default entry for FooterRequestQuoteLabel';
					}
					if(!$siteConfig->FooterContactUsLabel) {
						$siteConfig->FooterContactUsLabel = 'Contact Us<br />1300 877 767';
						$update[]= 'created default entry for FooterContactUsLabel';
					}
					if(!$siteConfig->FooterNewslettersLabel) {
						$siteConfig->FooterNewslettersLabel = 'Receive newsletters.';
						$update[]= 'created default entry for FooterNewslettersLabel';
					}
					if(!$siteConfig->owner->Tagline && isset($siteConfig->SiteLogoText)) {
						$siteConfig->owner->Tagline = $siteConfig->SiteLogoText;
						$update[]= 'created default entry for Tagline';
					}
					if(!$siteConfig->owner->Tagline) {
						$siteConfig->owner->Tagline = 'VoicePlus ensures you receive the best possible telecom service for your business. ';
						$update[]= "created default entry for Tagline";
					}

				}
				if(count($update)) {
					$siteConfig->write();
					DB::alteration_message($siteConfig->ClassName." created/updated: ".implode(" --- ",$update), 'created');
				}
			}
		}
	}
}
