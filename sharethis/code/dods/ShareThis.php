<?php
/**
 * Add a field to each SiteTree object and it's subclasses to enable Share icons.
 *@author nicolaas [at] sunnysideup.co.nz
 *@inspiration: Silverstripe Original Module - full credits to them.  We made our own to improve their module
 *
 *
 **/


class ShareThis extends DataObjectDecorator {

	/**
	* Include on all pages no matter what
	* @var boolean
	*/

	protected static $always_include = 0;
		static function set_always_include($value) {$value = self::clean_boolean_value($value); self::$always_include = $value;}

	/**
	* Include on all pages by default
	* @var boolean
	*/

	protected static $include_by_default = 0;
		static function set_include_by_default($value) {$value = self::clean_boolean_value($value); self::$include_by_default = $value;}

	/**
	* include a share this all in one
	* @var boolean
	*/

	protected static $share_this_all_in_one = 0;
		static function set_share_this_all_in_one($value) {$value = self::clean_boolean_value($value); self::$share_this_all_in_one = $value;}

	/**
	* show bookmark title next to icon
	* @var boolean
	*/

	protected static $show_title_with_icon = 0;
		static function set_show_title_with_icon($value) {$value = self::clean_boolean_value($value); self::$show_title_with_icon = $value;}

	/**
	* save the used one in the database
	* @var boolean
	*/

	protected static $use_data_object = 1;
		static function set_use_data_object($value) {$value = self::clean_boolean_value($value); self::$use_data_object = $value;}
		static function get_use_data_object() {return self::$use_data_object;}

	/**
	* specify alternative icons in the form of array($key => $filename, $key => $filename)
	* @var array
	*/

	protected static $alternate_icons = array();
		static function set_alternate_icons($array) {self::check_array($array); self::$alternate_icons = $array;}

	/**
	* use Black and White Mouse-Over effect
	* @var boolean
	*/

	protected static $use_bw_effect = array();
		static function set_use_bw_effect($value) {$value = self::clean_boolean_value($value); self::$use_bw_effect = $value;}

	/**
	* specify icons to be included, if left empty, this variable will be ignored
	* @var array
	*/

	protected static $icons_to_include = array();
		static function set_icons_to_include($array) {self::check_array($array);  self::$icons_to_include = $array;}
		static function get_icons_to_include() {return self::$icons_to_include;}
	/**
	* specify icons to be excluded, if left empty, this variable will be ignored
	* @var array
	*/

	protected static $icons_to_exclude = array();
		static function set_icons_to_exclude($array) {self::check_array($array);  self::$icons_to_exclude = $array;}
		static function get_icons_to_exclude() {return self::$icons_to_exclude;}


	private static function check_array($array) {
		if(!is_array($array)) {
			debug::show("parameter should be array");
		}
	}

	private static function clean_boolean_value($value) {
		if($value == false) {
			$value = 0;
		}
		if($value == true) {
		 $value = 1;
		}
		if($value == 1 || $value == 0) {
			return $value;
		}
		Debug::show("$value should be a 0 or 1");
	}

	function extraStatics(){
		if(self::$always_include) {
			return array();
		}
		else {
			return array(
				'db' =>  array('ShareIcons' => 'Boolean' ),
				'defaults' =>  array('ShareIcons' => self::$include_by_default),
			);
		}
	}


	function updateCMSFields(FieldSet &$fields) {
		if(!self::$always_include) {
			$fields->addFieldToTab("Root.Behaviour", new CheckboxField("ShareIcons","Show Share Icons on this page ?", self::$include_by_default));
		}
		$addedLinks = array();
		if($this->CanEditShareIcons()) {
			$addedLinksShort["edit"] = DataObjectOneFieldUpdateController::popup_link("ShareThisDataObject", "IncludeThisIcon");
			$addedLinksLong["edit"] = DataObjectOneFieldUpdateController::popup_link("ShareThisDataObject", "IncludeThisIconInExtendedList");
			$obj = singleton("ShareThisDataObject");
			$addedLinksShort["sort"] = $obj->dataObjectSorterPopupLink("IncludeThisIcon", 1);
			$addedLinksLong["sort"] = $obj->dataObjectSorterPopupLink("IncludeThisIconInExtendedList", 1);
		}
		if(count($addedLinksShort)) {
			$fields->addFieldToTab("Root.Behaviour", new LiteralField(
				"ShareIconsExplanationShort",
				'<p>Share Icon Settings - short list: '.implode(", ",$addedLinksShort).'.</p>'
			));
		}
		if(count($addedLinksLong)) {
			$fields->addFieldToTab("Root.Behaviour", new LiteralField(
				"ShareIconsExplanationLong",
				'<p>Share Icon Settings - long list: '.implode(", ",$addedLinksLong).'.</p>'
			));
		}
		return $fields;
	}

	/**
	* At the moment this method does nothing.
	*/

	function augmentSQL(SQLQuery &$query) {}


	/**
	* At the moment this method does nothing.
	*/
	function augmentDatabase() {
	}

	public function CanEditShareIcons() {
		if(class_exists("DataObjectSorterDOD")) {
			$obj = singleton("ShareThisDataObject");
			if($obj->hasExtension("DataObjectSorterDOD")) {
				return true;
			}
			else {
				USER_ERROR("you have installed DataObjectSorterDOD, but you have not extended ShareThisDataObject with DataObjectSorterDOD", E_USER_NOTICE);
			}
		}
		else {
			USER_ERROR("you may want to install the DataObjectSorter module", E_USER_NOTICE);
		}
	}

	public function ThisPageHasShareThis() {
		if(self::$always_include) {
			return true;
		}
		if(!$this->owner) {
			//just in case
			$this->owner = DataObject::get_one("SiteTree");
		}
		elseif(isset($this->owner->ShareIcons)) {
			return $this->owner->ShareIcons;
		}
		return false;
	}

	public function ShareIcons() {
		$bookmarks = $this->makeBookmarks($field = "IncludeThisIcon", $useAlternativeIcons = true);
		return $this->makeShareIcons($bookmarks);
	}

	public function ShareAllExpandedList() {
		Requirements::javascript("sharethis/javascript/ShareAllExpandedList.js");
		$bookmarks = $this->makeBookmarks($field = "IncludeThisIconInExtendedList", $useAlternativeIcons = false);
		return $this->makeShareIcons($bookmarks);
	}

	public function IncludeShareAll() {
		return self::$share_this_all_in_one;
	}

	public function ShareAll() {
		return ShareThisOptions::get_share_all($title, $link, $description);
	}


	/**
	 *returns array
	 **/


	protected function makeShareIcons($bookmarks){
		$doSet = new DataObjectSet();
		if($this->ThisPageHasShareThis() && $bookmarks){
			Requirements::themedCSS("SocialNetworking");
			Requirements::javascript("sharethis/javascript/shareThis.js");
			if(self::$use_bw_effect) {
				Requirements::customScript("sharethis.set_use_BW(true);", "ShareThisBWEffect");
			}
			foreach($bookmarks as $key => $bookmark){
				if(isset($bookmark["title"]) && isset($bookmark["url"])) {
					$itemArray = array();
					$itemArray["OnClick"] = isset($bookmark['click']) ? $bookmark['click'] : "";
					$itemArray["Title"] = $bookmark['title'];
					$itemArray["ShowTitle"] = self::$show_title_with_icon;
					$itemArray["URL"] = $bookmark["url"];
					$itemArray["Key"] = $key;
					if(isset($bookmark["icon"]) ) {
						$itemArray["ImageSource"] = $bookmark["icon"];
						$itemArray["UseStandardImage"] = 0;
					}
					else {
						$itemArray["ImageSource"] = "sharethis/images/icons/".$key.".png";
						$itemArray["UseStandardImage"] = 1;
					}
					$doSet->push(new ArrayData($itemArray));
				}
				else {
					debug::show("title of url not defined for $key");
				}
			}
		}
		return $doSet;
	}

	protected function makeBookmarks($field = "IncludeThisIcon", $useAlternativeIcons = true) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$finalBookmarks = array();
		if($this->ThisPageHasShareThis()) {
			$title = $this->owner->Title;
			$link = $this->owner->Link();
			$description = $this->owner->MetaDescription;
			$bookmarks = ShareThisOptions::get_page_specific_data($title, $link, $description);
			if(self::$use_data_object) {
				$objects = DataObject::get("ShareThisDataObject", "{$bt}{$field}{$bt} = 1", "{$bt}Sort{$bt} ASC, {$bt}Title{$bt} ASC");
				if($objects) {
					if($objects->count()) {
						foreach($objects as $obj) {
							if(isset($bookmarks[$obj->Title])) {
								$finalBookmarks[$obj->Title] = $bookmarks[$obj->Title];
							}
						}
					}
				}
			}
			else {
				$finalBookmarks = $bookmarks;
			}
			//find images
			if(count(self::$alternate_icons) && $useAlternativeIcons) {
				foreach(self::$alternate_icons as $key => $file) {
					if(!Director::fileExists($file)) {
						debug::show("Error in ShareIcons::set_alternate_icons, $file ($key) does not exist - should be a file name (e.g. images/icons/myicon.gif)");
					}
					elseif(isset($finalBookmarks[$key]) && isset($finalBookmarks[$key])) {
						$finalBookmarks[$key]["icon"] = $file;
					}
				}
			}
		}
		if(!count($finalBookmarks)) {
			$finalBookmarks = array();
		}
		return $finalBookmarks;
	}

//DEPRECIATED!

	/**
	* Boolean that determines whether icons are transparent or not.
	* @var boolean
	* DEPRECIATED!
	*/
	static $IconTransparent = false;//LEGACY

	/**
	* Boolean that determines whether to show the title of the site next to
	* the icon.
	* @var boolean
	*/
	static $ShowTitle = true;//LEGACY

	/**
	* Sets the enabled icons list. May contain any of the following:
	* - email, print, digg, reddit, delicious, furl, ma.gnolia, newsvine, live, myweb, google, stumbleupon, simpy, facebook, socialmarker
	*
	* Example: ShareIcons::$EnabledIcons = array('digg', 'delicious');
	* @var array
	*/
	static $EnabledIcons = array(); //LEGACY

	/**
	* Allows you to specify alternate images for each of the icons. Will be
	* used only if specified, otherwise the default will be used. If this is
	* used, it won't try to get transparent/non-transparent images.
	* @var array
	*/


}
