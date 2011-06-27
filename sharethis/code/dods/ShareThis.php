<?php
/**
 * Add a field to each SiteTree object and it's subclasses to enable Share icons.
 *@author nicolaas [at] sunnysideup.co.nz
 *@inspiration: Silverstripe Original Module - full credits to them.  We made our own to improve their module
 *
 *
 **/


class ShareThis extends SiteTreeDecorator {


	/**
	* include a share this all in one
	* @var boolean
	*/

	protected static $share_this_all_in_one = 0;
		static function set_share_this_all_in_one($value) {$value = self::clean_boolean_value($value); self::$share_this_all_in_one = $value;}


	/**
	* use BW icons
	* @var boolean
	*/

	protected static $use_bw_share_this_effect = "";
		static function set_use_bw_share_this_effect($s) {self::$use_bw_share_this_effect = $s;}

	/**
	* specify icons to be included, if left empty, this variable will be ignored
	* We have this variable so that you can setup a bunch of default icons
	* @var array
	*/

	protected static $share_this_icons_to_include = array();
		static function set_share_this_icons_to_include($array) {self::check_array($array);  self::$share_this_icons_to_include = $array;}
		static function get_share_this_icons_to_include() {return self::$share_this_icons_to_include;}

	/**
	* specify icons to be excluded, if left empty, this variable will be ignored
	* We have this variable so that you can setup a bunch of default icons
	* @var array
	*/

	protected static $share_this_icons_to_exclude = array();
		static function set_share_this_icons_to_exclude($array) {self::check_array($array);  self::$share_this_icons_to_exclude = $array;}
		static function get_share_this_icons_to_exclude() {return self::$share_this_icons_to_exclude;}


	private static function check_array($array) {
		if(!is_array($array)) {
			debug::show("parameter should be array");
		}
	}


	function extraStatics(){
		return array(
			'db' =>  array('ShareIcons' => 'Boolean' )
		);
	}


	function updateCMSFields(FieldSet &$fields) {
		if(!$this->SiteConfig()->AlwaysIncludeShareThisLinks) {
			$fields->addFieldToTab("Root.SocialMedia", new HeaderField("ShareThisHeader", "Allow users to share this page"));			
			$fields->addFieldToTab("Root.SocialMedia", new CheckboxField("ShareIcons","Show Share Icons on this page", $this->SiteConfig()->IncludeByDefault));
		}
		$fields->addFieldToTab("Root.SocialMedia", new LiteralField('LinkToSiteConfigSocialMedia', '<p>Note: Also review the social media settings in the <a href="/admin/show/root/">Site Config</a>.</p>'));
		$list = ShareThisOptions::get_all_options($title = $this->owner->Title, $link = $this->owner->Link(), $description = $this->owner->MetaDescription);
		$fields->addFieldToTab("Root.SocialMedia", new HeaderField("ShareThisNow", "Share this yourself ..."));			
		$html = "<div>";
		foreach($list as $key => $innerArray) {
			if(!isset($innerArray["click"])) {
				$html .= '<span><a href="'.$innerArray["url"].'" target="_blank" style="whitespace: nowrap; display: inline-block;"><img src="/'.SHARETHIS_DIR.'/images/icons/'.$key.'.png" alt="'.$key.'" /> '.$innerArray["title"].'</a></span> &nbsp; &nbsp; ';
			}
		}
		$html .= '</div>';
		$fields->addFieldToTab("Root.SocialMedia", new LiteralField('ShareNow', $html));
		
	}

	public function ThisPageHasShareThis() {
		if($this->SiteConfig()->AlwaysInclude) {
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
		Requirements::javascript(SHARETHIS_DIR."/javascript/ShareAllExpandedList.js");
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
			Requirements::javascript(SHARETHIS_DIR."/javascript/shareThis.js");
			if(self::$use_bw_share_this_effect) {
				Requirements::customScript("sharethis.set_use_BW(true);", "ShareThisBWEffect");
			}
			foreach($bookmarks as $key => $bookmark){
				if(isset($bookmark["title"]) && isset($bookmark["url"])) {
					$itemArray = array();
					$itemArray["OnClick"] = isset($bookmark['click']) ? $bookmark['click'] : "";
					$itemArray["Title"] = $bookmark['title'];
					$itemArray["URL"] = $bookmark["url"];
					$itemArray["Key"] = $key;
					$itemArray["Key"] = $key;
					if(isset($bookmark["icon"]) ) {
						$itemArray["ImageSource"] = $bookmark["icon"];
						$itemArray["UseStandardImage"] = 0;
					}
					else {
						$itemArray["ImageSource"] = "sharethis/images/icons/".$key.".png";
						$itemArray["UseStandardImage"] = 1;
					}
					$itemArray["ImageSourceOver"] = str_replace(array(".png", ".gif", ".jpg"), array("_over.png", "_over.gif", "_over.jpg"), $bookmark["icon"]);
					$doSet->push(new ArrayData($itemArray));
				}
				else {
					debug::show("title of url not defined for $key");
				}
			}
		}
		return $doSet;
	}

	protected function makeBookmarks($field = "IncludeThisIcon") {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$finalBookmarks = array();
		if($this->ThisPageHasShareThis()) {
			$title = $this->owner->Title;
			$link = $this->owner->Link();
			$description = $this->owner->MetaDescription;
			$bookmarks = ShareThisOptions::get_page_specific_data($title, $link, $description);
			$objects = DataObject::get("ShareThisDataObject", "{$bt}{$field}{$bt} = 1", "{$bt}Sort{$bt} ASC, {$bt}Title{$bt} ASC");
			if($objects) {
				if($objects->count()) {
					foreach($objects as $obj) {
						if(isset($bookmarks[$obj->Title])) {
							$finalBookmarks[$obj->Title] = $bookmarks[$obj->Title];
							if($obj->AlternativeIconID && $obj->AlternativeIcon()->exists()) {
								$finalBookmarks[$obj->Title]["icon"] = $obj->AlternativeIcon()->Link();
							}
						}
					}
				}
			}
			else {
				$finalBookmarks = $bookmarks;
			}
		}
		return $finalBookmarks;
	}

	protected function SiteConfig(){
		return SiteConfig::current_site_config();
	}
	function populateDefaults() {
		$this->owner->HasSocialNetworkingLinks = $this->SiteConfig()->IncludeByDefaultShareThisLinks;
	}

}
