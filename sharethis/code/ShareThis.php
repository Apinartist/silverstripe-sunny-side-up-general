<?php
	/**
	* Add a field to each SiteTree object and it's subclasses to enable Share icons.
	*
	* You can (and should) customise this class by adding the following options
	* to your _config.php file:
	* - {@link ShareIcons::$EnabledIcons}: An array of the enabled icons
	* - {@link ShareIcons::$IconTransparent}: Use transparent icons (or not)
	* - {@link ShareIcons::$ShowTitle}: Show the title next to the icon (or not)
	*
	*
	*/


class ShareThis extends DataObjectDecorator {


	/**
	* Boolean that determines whether icons are transparent or not.
	* @var boolean
	*/
	static $IconTransparent = false;

	/**
	* Boolean that determines whether to show the title of the site next to
	* the icon.
	* @var boolean
	*/
	static $ShowTitle = true;

	/**
	* Sets the enabled icons list. May contain any of the following:
	* - email
	* - print
	* - digg
	* - reddit
	* - delicious
	* - furl
	* - ma.gnolia
	* - newsvine
	* - live
	* - myweb
	* - google
	* - stumbleupon
	* - simpy
	* - facebook
	* - socialmarker
	*
	* Example: ShareIcons::$EnabledIcons = array('digg', 'delicious');
	* @var array
	*/
	static $EnabledIcons = array();

	/**
	* Allows you to specify alternate images for each of the icons. Will be
	* used only if specified, otherwise the default will be used. If this is
	* used, it won't try to get transparent/non-transparent images.
	* @var array
	*/
	static $alternate_icons = array();

	/**
	* Include on all pages by default
	* @var boolean
	*/
	static $include_by_default = false;

	/**
	* Include on all pages (NO field required)
	* @var boolean
	*/

	static $always_include = true;

	function extraDBFields(){
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


	static function set_include_by_default($value) {
		self::$include_by_default = $value;
	}

	static function set_always_include($value) {
		self::$always_include = $value;
	}
	/**
	* Add a share icons field to cms
	* This method doesn't seemed to get automatically called. had to add a static method to config.
	*/

	function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.Behaviour", new CheckboxField("ShareIcons","Show Share Icons on this page ?"));
		return $fields;
	}

	/**
	* At the moment this method does nothing.
	*/

	function augmentSQL(SQLQuery &$query) {
	}


	/**
	* At the moment this method does nothing.
	*/
	function augmentDatabase() {
	}

	function thisPageHasShareThis() {
		if($this->owner) {
			if(self::$always_include) {
				return true;
			}
			elseif(isset($this->owner->ShareIcons)) {
				if($this->owner->ShareIcons) {
					return true;
				}
			}
		}
		return false;
	}

	function Icons(){
		$doSet = new DataObjectSet();
		if($this->thisPageHasShareThis()){
			Requirements::css("sharethis/css/ShareThis.css");
			Requirements::javascript("sharethis/javascript/shareThis.js");
			$format = self::$IconTransparent ? "_trans" : "";
			$bookmarks = $this->bookmarks();
			foreach(self::$EnabledIcons as $EnabledIcon){
				if(isset($EnabledIcon["title"]) && isset($EnabledIcon["url"])) {
					$itemArray = array();
					$itemArray["OnClick"] = isset($bookmarks[$EnabledIcon]['click']) ? $bookmarks[$EnabledIcon]['click'] : "";
					$itemArray["Title"] = $bookmarks[$EnabledIcon]['title'];
					$itemArray["URL"] = $bookmarks[$EnabledIcon]["url"];
					$itemArray["Key"] = $EnabledIcon;
					if(isset(self::$alternate_icons["$EnabledIcon"]) && Director::fileExists(self::$alternate_icons["$EnabledIcon"])) {
						$itemArray["ImageSource"] = self::$alternate_icons["$EnabledIcon"];
					}
					else {
						$itemArray["ImageSource"] = "sharethis/images/icons/".$EnabledIcon.$format.".gif";
					}
					$doSet->push(new ArrayData($itemArray));
				}
			}
		}
		return $doSet;
	}

	private function bookmarks() {
		$bookmarks = array();
		if($this->owner) {
			$page_url = urlencode(
				Director::absoluteBaseURL().
				$this->owner->URLSegment
			);
			$page_title = urlencode($this->owner->Title);
			$bookmarks = array(
			"email" => array(
				 "url" => "mailto:?body=$page_url",
				 "title" => "Email"),
			"print" => array(
				 "url" => "#",
				 "click" => "window.print(); return false;",
				 "title" => "Print"),
			"digg" => array(
				 "url" => "http://digg.com/submit?".htmlentities("phase=2&url=$page_url&title=$page_title"),
				 "title" => "Digg this!"),
			"reddit" => array(
				 "url" => "http://reddit.com/submit?".htmlentities("url=$page_url&title=$page_title"),
				 "title" => "Reddit!"),
			"delicious" => array(
				 "url" => "http://del.icio.us/post?".htmlentities("v=4&noui&jump=close&url=$page_url&title=$page_title"),
				 "title" => "Add to del.icio.us"),
			"furl" => array(
				 "url" => "http://www.furl.net/storeIt.jsp?".htmlentities("t=$page_title&u=$page_url"),
				 "title" => "Furl this!"),
			"ma.gnolia" => array(
				 "url" => "http://ma.gnolia.com/bookmarklet/add?".htmlentities("url=$page_url&title=$page_title"),
				 "title" => "Add to ma.gnolia"),
			"newsvine" => array(
				 "url" => "http://www.newsvine.com/_tools/seed".htmlentities("&save?u=$page_url&h=$page_title"),
				 "title" => "Save to Newsvine!"),
			"live" => array(
				 "url" => "https://favorites.live.com/quickadd.aspx?".htmlentities("marklet=1&mkt=en-us&url=$page_url&title=$page_title&top=1"),
				 "title" => "Add to Windows Live"),
			"myweb" => array(
				 "url" =>  "http://myweb.yahoo.com/myresults/bookmarklet?".htmlentities("t=$page_title&u=$page_url&ei=UTF"),
				 "title" => "Add to Yahoo MyWeb"),
			"google" => array(
				 "url" =>  "http://www.google.com/bookmarks/mark?".htmlentities("op=edit&output=popup&bkmk=$page_url&title=$page_title"),
				 "title" => "Googlize this post!"),
			"stumbleupon" => array(
				 "url" => "http://www.stumbleupon.com/submit?".htmlentities("url=$page_url&title=$page_title"),
				 "title" => "Stumble It!"),
			"simpy" => array(
				 "url" => "http://simpy.com/simpy/LinkAdd.do?".htmlentities("title=$page_title&href=$page_url"),
				 "title" => "Add to Simpy"),
			"favourites" => array(
				 "url" => "#",
				 "click" => "bookmark('$page_url', '$page_title'); return false;",
				 "title" => "Add to favourites (Internet Explorer Only)"),
			"facebook" => array(
				 "url" => "http://www.facebook.com/share.php?".htmlentities("u=$page_url&t=$page_title"),
				 "title" => "Share on Facebook"),
			"socialmarker" => array(
				 "url" => "http://www.socialmarker.com/?".htmlentities("link=$page_url&title=$page_title"),
				 "title" => "Bookmark Elsewhere")
			);
		}
		return $bookmarks;
	}

}
