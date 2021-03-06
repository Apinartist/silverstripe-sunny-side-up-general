<?php
/**
*
* @Author Martijn van Nieuwenhoven & Nicolaas Francken
*
* @Silverstripe version 2.3.2
* @package metatags
*
**/

class MetaTagAutomation extends SiteTreeDecorator {

	/**
	 * pop-ups and form interaction
	 * @var Boolean
	 */
	protected static $disable_update_popup = false;
		static function set_disable_update_popup($b) {self::$disable_update_popup = $b;}

	/**
	 * length of auto-generated meta descriptions in header
	 * @var Int
	 */
	protected static $meta_desc_length = 24;
		static function set_meta_desc_length($i) {self::$meta_desc_length = $i;}
		static function get_meta_desc_length() {return self::$meta_desc_length;}

	/**
	 * exclude meta keywords from header altogether
	 * @var Boolean
	 **/
	protected static $hide_keywords_altogether = true;
		static function set_hide_keywords_altogether($b) {self::$hide_keywords_altogether = $b; }
		static function get_hide_keywords_altogether() {return self::$hide_keywords_altogether; }


	/**
	 * google fonts to be used
	 * @var Array
	 **/
	protected static $google_font_collection = array();
		static function add_google_font($s) {self::$google_font_collection[$s] = $s;}
		static function remove_google_font($s) {unset(self::$google_font_collection[$s]);}
		static function get_google_font_collection() {return self::$google_font_collection;}

	/**
	 * @var Boolean
	 **/
	protected static $use_themed_favicon = false;
		static function set_use_themed_favicon($b) {self::$use_themed_favicon = $b;}
		static function get_use_themed_favicon() {return self::$use_themed_favicon;}

	/**
	 * standard SS method
	 * @var Array
	 **/
	public function extraStatics() {
		return array (
			'db' => array(
				'AutomateMetatags' => 'Boolean',
			),
			'defaults' => array(
				'AutomateMetatags' => true
			)
		);
	}

	/**
	 * standard SS method
	 * @var Array
	 **/
	public function updateCMSFields(FieldSet &$fields) {
		if(self::get_hide_keywords_altogether()) {
			$fields->removeFieldFromTab("Root.Content.Metadata", "MetaKeywords");
		}
		$automatedFields =  $this->updatedFieldsArray();
		if(count($automatedFields)) {
			$updated_field_string = " (the following fields will be automatically updated: <i>".implode("</i>, <i>", $automatedFields)."</i>).";
			$fields->addFieldToTab('Root.Content.Metadata', new CheckboxField('AutomateMetatags', _t('MetaManager.UPDATEMETA','Allow Meta (Search Engine) Fields to be updated automatically? '). $updated_field_string), "URL");
			if($this->owner->AutomateMetatags) {
				foreach($automatedFields as $fieldName => $fieldTitle) {
					$newField = $fields->dataFieldByName($fieldName)->performReadonlyTransformation();
					$newField->setTitle($newField->Title()." (automatically updated when you save this page)");
					$fields->replaceField($fieldName, $newField);
				}
			}
		}
		$fields->removeFieldFromTab("Root.Content.Metadata", "ExtraMeta");
		if(1 == self::$disable_update_popup){
			Requirements::clear('sapphire/javascript/UpdateURL.js');
			Requirements::javascript(SS_METATAGS_DIR.'/javascript/UpdateURL.js');
		}
		$linkToManager = "/" . MetaTagCMSControlPages::get_url_segment() ."/";
		$fields->addFieldToTab('Root.Content.Metadata', new LiteralField("LinkToManagerHeader", "<p>Open the Meta Tag Manager to <a href=\"$linkToManager\" target=\"_blank\">Review and Edit</a> the Meta Data for all pages on this site. Also make sure to review the general <a href=\"/admin/show/root/\">settings for Search Engines</a>.</p>"), "URL");
		if($this->owner->URLSegment == "home") {
			$newField = $fields->dataFieldByName("URLSegment");
			$newField->setRightTitle("Careful: changing the URL from 'home' to anything else means that this page will no longer be the home page");
			$fields->replaceField("URLSegment", $newField);
		}
		return $fields;
	}

	/**
	 * Update Metadata fields function
	 */
	public function onBeforeWrite () {

		$siteConfig = SiteConfig::current_site_config();
		// if UpdateMeta checkbox is checked, update metadata based on content and title
		// we only update this from the CMS to limit slow-downs in programatic updates
		if(isset($_REQUEST['AutomateMetatags']) && $_REQUEST['AutomateMetatags']){
			if($siteConfig->UpdateMetaTitle){
				// Empty MetaTitle
				$this->owner->MetaTitle = '';
				// Check for Content, to prevent errors
				if($this->owner->Title){
					$this->owner->MetaTitle = $this->cleanInput($this->owner->Title, 0);
				}
			}
			if($siteConfig->UpdateMenuTitle){
				// Empty MetaTitle
				$this->owner->MenuTitle = '';
				// Check for Content, to prevent errors
				if($this->owner->Title){
					$this->owner->MenuTitle = $this->cleanInput($this->owner->Title, 0);
				}
			}
			if($siteConfig->UpdateMetaDescription && self::$meta_desc_length ){
				// Empty MetaDescription
				// Check for Content, to prevent errors

				if($this->owner->Content){
					//added a few hacks here
					$contentField = DBField::create("Text", $this->owner->Content, "MetaDescription");
					$flex = ceil(MetaTagAutomation::get_meta_desc_length() / 2)+5;
					$summary = $contentField->Summary(MetaTagAutomation::get_meta_desc_length(), $flex);
					$summary = str_replace("<br />", " ", $summary);
					$this->owner->MetaDescription = strip_tags($summary);
				}
			}
		}

 	}


	public function onAfterWrite(){
		// TODO : find a nicer way to reload the page and when exactly it needs reloading
		//LeftAndMain::ForceReload ();
		parent::onAfterWrite ();
		$siteConfig = SiteConfig::current_site_config();
		$oldMetaTitle = $this->owner->MetaTitle;
		if($siteConfig->PrependToMetaTitle) {
			if(strpos($this->owner->MetaTitle, $siteConfig->PrependToMetaTitle) === 0) {
				$this->owner->MetaTitle = str_replace($siteConfig->PrependToMetaTitle, "", $this->owner->MetaTile);
			}
		}
		if($siteConfig->AppendToMetaTitle) {
			if(strpos($this->owner->MetaTitle, $siteConfig->AppendToMetaTitle) === (strlen($this->owner->MetaTitle) - strlen($siteConfig->AppendToMetaTitle))) {
				$this->owner->MetaTitle = str_replace($siteConfig->AppendToMetaTitle, "", $this->owner->MetaTile);
			}
		}
		if($this->owner->MetaTitle != $oldMetaTitle) {
			$this->owner->write();
		}
	}

	private function updatedFieldsArray(){
		$siteConfig = SiteConfig::current_site_config();
		$updateDatedFieldArray = array();
		if(self::$disable_update_popup) 					{ $updateDatedFieldArray["URLSegment"] = _t('SiteTree.URLSegment','URL Segment ');}
		if($siteConfig->UpdateMenuTitle) 	  			{ $updateDatedFieldArray["MenuTitle"] = _t('SiteTree.MENUTITLE','Menu Title '); }
		if($siteConfig->UpdateMetaTitle) 					{ $updateDatedFieldArray["MetaTitle"] = _t('SiteTree.METATITLE','Title '); }
		if($siteConfig->UpdateMetaDescription) 	  { $updateDatedFieldArray["MetaDescription"] = _t('SiteTree.METADESCRIPTION','Description '); }
		return $updateDatedFieldArray;
	}

	function populateDefaults () {
		$this->owner->AutomateMetatags = true;
	}

	/*
	private function SiteConfigVar($fieldName) {
		if($siteConfig = SiteConfig::current_site_config()) {
			if(isset($siteConfig->$fieldName)) {
				return $siteConfig->$fieldName;
			}
		}
		return false;
	}
	*/
	private function cleanInput($string, $numberOfWords = 0) {
		$newString = str_replace("&nbsp;", "", $string);
		$newString = str_replace("&amp;", " and ", $newString);
		$newString = str_replace("&ndash;", " - ", $newString);
		$newString = strip_tags(str_replace('<', ' <', $newString));
		if($numberOfWords) {
			$textFieldObject = Text::create("Text", $newString);
			if($textFieldObject) {
				$newString = strip_tags($textFieldObject->LimitWordCountXML($numberOfWords));
			}
		}
		$newString = html_entity_decode($newString, ENT_QUOTES);
		$newString = html_entity_decode($newString, ENT_QUOTES);
		return $newString;
	}

	function requireDefaultRecords(){
		$folder = SSViewer::current_theme();
		if($folder) {
			$destinationFile = Director::baseFolder()."/themes/".$folder."/css/editor.css";
			$baseFile = Director::baseFolder(). "/".SS_METATAGS_DIR."/css/editor.css";
			if(!file_exists($destinationFile) && file_exists($baseFile)) {
				copy($baseFile, $destinationFile);
			}
		}
		$folder = SSViewer::current_theme();
		if($folder) {
			$destinationFile = Director::baseFolder()."/themes/".$folder."/css/reset.css";
			$baseFile = Director::baseFolder(). "/".SS_METATAGS_DIR."/css/reset.css";
			if(!file_exists($destinationFile) && file_exists($baseFile)) {
				copy($baseFile, $destinationFile);
			}
		}
	}

}

class MetaTagAutomation_controller extends Extension {

	/* combined files */

	protected static $folder_for_combined_files = "assets";
		static function set_folder_for_combined_files($s) {self::$folder_for_combined_files = $s;}

	protected static $combine_css_files_into_one = false;
		public static function set_combine_css_files_into_one($s) {self::$combine_css_files_into_one = $s;}

	protected static $combine_js_files_into_one = false;
		public static function set_combine_js_files_into_one($s) {self::$combine_js_files_into_one = $s;}

	/**
	 * add all the basic js and css files - call from Page::init()
	 */
	private static $metatags_building_completed = false;

	function addBasicMetatagRequirements($additionalJS = array(), $additionalCSS = array(), $force = false) {
		if(!self::$metatags_building_completed || $force) {
			self::$metatags_building_completed = true;
			$themeFolder = $this->getThemeFolder();
			$cssArrayLocationOnly = array();
			$jsArray =
				array(
					THIRDPARTY_DIR."/jquery/jquery.js",
					$this->owner->project().'/javascript/j.js'
				);
			array_merge($jsArray, $additionalJS);
			$cssArray =
				array(
					array("media" => null, "location" => $themeFolder.'css/reset.css'),
					array("media" => null, "location" => $themeFolder.'css/typography.css'),
					array("media" => null, "location" => $themeFolder.'css/layout.css'),
					array("media" => null, "location" => $themeFolder.'css/form.css'),
					array("media" => null, "location" => $themeFolder.'css/menu.css'),
					array("media" => "print", "location" => $themeFolder.'css/print.css'),
					array("media" => null, "location" => $themeFolder.'css/individualPages.css')
				);
			array_merge($cssArray, $additionalCSS);
			foreach($jsArray as $js) {
				Requirements::javascript($js);
			}
			foreach($cssArray as $cssArraySub) {
				Requirements::css($cssArraySub["location"], $cssArraySub["media"]);
				$cssArrayLocationOnly[] = $cssArraySub["location"];
			}
			if(self::$combine_css_files_into_one) {
				Requirements::combine_files(self::$folder_for_combined_files."/MetaTagAutomation.css",$cssArrayLocationOnly);
			}
			if(self::$combine_js_files_into_one) {
				$prototypeArray =
					array(
						"sapphire/javascript/Validator.js",
						THIRDPARTY_DIR."/prototype/prototype.js",
						THIRDPARTY_DIR."/behaviour/behaviour.js",
						"sapphire/javascript/prototype_improvements.js"
					);
				Requirements::combine_files(self::$folder_for_combined_files."/MetaTagAutomationPrototype.js", $prototypeArray);
				Requirements::combine_files(self::$folder_for_combined_files."/MetaTagAutomation.js", $jsArray);
			}
			$googleFontArray = MetaTagAutomation::get_google_font_collection();
			if($googleFontArray && count($googleFontArray)) {
				$protocol = Director::protocol();
				foreach($googleFontArray as $font) {
					Requirements::insertHeadTags('<link href="' . $protocol . 'fonts.googleapis.com/css?family=' . urlencode($font) . '" rel="stylesheet" type="text/css" />');
				}
			}
		}
	}

	/**
	 * this function will add more metatags to your template - make sure to add it at the start of your metatags
	 */

	function ExtendedMetatags($includeTitle = true, $addExtraSearchEngineData = true) {
		$this->addBasicMetatagRequirements();
		$themeFolder = $this->getThemeFolder();
		$tags = "";
		$page = $this->owner;
		$siteConfig = SiteConfig::current_site_config();
		$title = $page->MetaTitle ? $page->MetaTitle : $page->Title;
		if(!MetaTagAutomation::get_hide_keywords_altogether()) {
			$keywords = Convert::raw2xml(($page->MetaKeywords) ? $page->MetaKeywords : $page->Title );
		}
		if($page->MetaDescription) {
			$description = '
			<meta name="description" http-equiv="description" content="'.Convert::raw2att($page->MetaDescription).'" />';
			$noopd = '';
		}
		else {
			$noopd = "NOODP, ";
			$description = '';
		}
		$lastEdited = new SS_Datetime();
		$lastEdited->value = $page->LastEdited;

		//use base url rather than / so that sites that aren't a run from the root directory can have a favicon
		$faviconBase = Director::baseURL();
		if(MetaTagAutomation::get_use_themed_favicon()) {
			$faviconBase .= $themeFolder;
		}
		if($includeTitle) {
			$titleTag = '
			<title>'.trim(Convert::raw2att($siteConfig->PrependToMetaTitle.' '.$page->MetaTitle.' '.$siteConfig->AppendToMetaTitle)).'</title>';
		}
		else {
			$titleTag = '';
		}
		$tags .= '
			<meta charset="utf-8" />
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />'.
			$titleTag
			.'
			<link rel="icon" href="'.$faviconBase.'favicon.ico" type="image/x-icon" />
			<link rel="apple-touch-icon" href="'.$faviconBase.'apple-touch-icon.png" type="image/x-icon" />
			<link rel="shortcut icon" href="'.$faviconBase.'favicon.ico" type="image/x-icon" />';
		if(!MetaTagAutomation::get_hide_keywords_altogether()) {
			$tags .= '
			<meta name="keywords" http-equiv="keywords" content="'.Convert::raw2att($keywords).'" />';
		}
		if(!$page->ExtraMeta && $siteConfig->ExtraMeta) {
			$page->ExtraMeta = $siteConfig->ExtraMeta;
		}
		if(!$siteConfig->MetaDataCountry) {$siteConfig->MetaDataCountry = Geoip::countryCode2name(Geoip::$default_country_code);}
		if(!$siteConfig->MetaDataCopyright) {$siteConfig->MetaDataCopyright = $siteConfig->Title;}
		if($addExtraSearchEngineData) {
			$tags .= '
			<meta name="robots" content="'.$noopd.'all, index, follow" />
			<meta name="googlebot" content="'.$noopd.'all, index, follow" />
			<meta name="copyright" content="'.$siteConfig->MetaDataCopyright.'" />
			<meta name="coding" content="'.$siteConfig->MetaDataCoding.'" />
			<meta name="design" content="'.$siteConfig->MetaDataDesign.'" />
			<meta name="date-modified-yyyymmdd" content="'.$lastEdited->Format("Ymd").'" />
			<meta name="country" content="'.$siteConfig->MetaDataCountry.'" />
			<meta http-equiv="imagetoolbar" content="no" />
			<meta name="viewport" content="width=device-width,initial-scale=1" />
			<meta http-equiv="Content-Language" content="'.i18n::get_locale().'" />
			'.$page->ExtraMeta.
			$description;
		}
		Requirements::insertHeadTags('<meta http-equiv="X-UA-Compatible" content="ie=edge,chrome=1" />', 'use-ie-edge');
		return $tags;
	}

	//maybe replaced with something more universal (e.g. SSViewer::get_theme_folder())
	private function getThemeFolder() {
		return SSViewer::current_theme() ? THEMES_DIR . "/" . SSViewer::current_theme() ."/" : $this->owner->project() ."/" ;
	}


}
