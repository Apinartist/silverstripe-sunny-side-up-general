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


	protected $bookmarks = array();

	function updateCMSFields(FieldSet &$fields) {
		if(!self::$always_include) {
			$fields->addFieldToTab("Root.Behaviour", new CheckboxField("ShareIcons","Show Share Icons on this page ?", self::$include_by_default));
		}
		$addedLinks = array();
		if($this->EditLink()) {
			$addedLinks["edit"]  = $this->EditLink();
		}
		if($this->SortLink()) {
			$addedLinks["sort"]  = $this->SortLink();
		}
		if(count($addedLinks)) {
			$fields->addFieldToTab("Root.Behaviour", new LiteralField(
				"ShareIconsExplanation",
				'<p>Share Icon Settings: '.implode(", ",$addedLinks).'.</p>'
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
		}
		else {
			USER_ERROR("you may want to install the DataObjectSorter module", E_USER_NOTICE);
		}
	}

	public function EditLink() {
		if($this->CanEditShareIcons()) {
			return DataObjectOneFieldUpdateController::popup_link("ShareThisDataObject", "IncludeThisIcon");
		}
	}

	public function SortLink() {
		if($obj = $this->CanEditShareIcons()) {
			$obj = singleton("ShareThisDataObject");
			return $obj->dataObjectSorterPopupLink("IncludeThisIcon", 1);
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


	public function ShareIcons(){
		$doSet = new DataObjectSet();
		if($this->ThisPageHasShareThis()){
			Requirements::themedCSS("SocialNetworking");
			Requirements::javascript("sharethis/javascript/shareThis.js");
			if(self::$use_bw_effect) {
				Requirements::customScript("sharethis.set_use_BW(true);", "ShareThisBWEffect");
			}
			$this->bookmarks = $this->makeBookmarks();
			if($this->bookmarks) {
				foreach($this->bookmarks as $key => $bookmark){
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
		}
		return $doSet;
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

	protected function makeBookmarks() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if(!count($this->bookmarks) && $this->ThisPageHasShareThis()) {
			$title = $this->owner->Title;
			$link = $this->owner->Link();
			$description = $this->owner->MetaDescription;
			$bookmarks = ShareThisOptions::get_page_specific_data($title, $link, $description);

			//which ones do we include
			$originalArray = array();
			foreach($bookmarks as $key => $ignore) {
				$originalArray[$key] = $key;
			}
			unset($ignore);
			if(self::$use_data_object) {
				$this->bookmarks = null;
				$this->bookmarks = array();
				$objects = DataObject::get("ShareThisDataObject", "{$bt}IncludeThisIcon{$bt} = 1", "{$bt}Sort{$bt} ASC, {$bt}Title{$bt} ASC");
				if($objects) {
					if($objects->count()) {
						foreach($objects as $obj) {
							$this->bookmarks[$obj->Title] = $bookmarks[$obj->Title];
						}
					}
				}
			}
			else {
				$this->bookmarks = $bookmarks;
			}
			//find images
			if(count(self::$alternate_icons)) {
				foreach(self::$alternate_icons as $key => $file) {
					if(!isset($originalArray[$key])) {
						debug::show("Error in ShareIcons::set_alternate_icons, $key does not exist in bookmark list");
					}
					elseif(!Director::fileExists($file)) {
						debug::show("Error in ShareIcons::set_alternate_icons, $file ($key) does not exist - should be a file name (e.g. images/icons/myicon.gif)");
					}
					elseif(isset($this->bookmarks[$key])) {
						$this->bookmarks[$key]["icon"] = $file;
					}
				}
			}
		}
		if(!count($this->bookmarks)) {
			$this->bookmarks = array();
		}
		return $this->bookmarks;
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
