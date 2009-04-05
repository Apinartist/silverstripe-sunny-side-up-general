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
	* - email, print, digg, reddit, delicious, furl, ma.gnolia, newsvine, live, myweb, google, stumbleupon, simpy, facebook, socialmarker
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

	static $share_this_all_in_one = false;

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

	static function set_share_this_all_in_one($value) {
		self::$share_this_all_in_one = $value;
	}

	static function set_always_include($value) {
		self::$always_include = $value;
	}

	var $pageURL = '';
	var $pageTitle = '';
	var $pageIcon = '';

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

	public function ThisPageHasShareThis() {
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

	public function Icons(){
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

			/*
				summary: (required) utf-8 string, defaults to document.title
				content: (optional) utf-8 string, defaults to null
				updated: (optional) ISO 8601 date, defaults to document.lastModified
				published: (optional) ISO 8601 date, defaults to null
				author: currently not implemented
				category: currently not implemented
			*/

	public function ShareAll() {
		if(self::$share_this_all_in_one) {
			$this->pageIcon = urlencode(Director::absoluteBaseURL().'favicon.ico');
			return '
<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#&amp;type=website" />
<script type="text/javascript">
	SHARETHIS.addEntry(
		{
			title:"'.urldecode($this->pageTitle).'",
			summary:"'.urldecode($this->pageTitle).'",
			url:"'.urldecode($this->pageIcon).'",
			icon:"'.urldecode($this->pageURL).'"
		},
		{button:true}
	);
</script>';
		}
		else {
			return false;
		}
	}

	private function bookmarks() {
		$bookmarks = array();
		if($this->owner) {
			$this->pageURL = urlencode(Director::absoluteBaseURL().$this->owner->URLSegment);
			$this->pageTitle = urlencode($this->owner->Title);
			$bookmarks = array(
			"email" => array(
				 "url" => "mailto:?body=".$this->pageURL."",
				 "title" => "Email"),
			"print" => array(
				 "url" => "#",
				 "click" => "window.print(); return false;",
				 "title" => "Print"),
			"digg" => array(
				 "url" => "http://digg.com/submit?".htmlentities("phase=2&url=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Digg this!"),
			"reddit" => array(
				 "url" => "http://reddit.com/submit?".htmlentities("url=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Reddit!"),
			"delicious" => array(
				 "url" => "http://del.icio.us/post?".htmlentities("v=4&noui&jump=close&url=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Add to del.icio.us"),
			"furl" => array(
				 "url" => "http://www.furl.net/storeIt.jsp?".htmlentities("t=".$this->pageTitle."&u=".$this->pageURL.""),
				 "title" => "Furl this!"),
			"ma.gnolia" => array(
				 "url" => "http://ma.gnolia.com/bookmarklet/add?".htmlentities("url=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Add to ma.gnolia"),
			"newsvine" => array(
				 "url" => "http://www.newsvine.com/_tools/seed".htmlentities("&save?u=".$this->pageURL."&h=".$this->pageTitle.""),
				 "title" => "Save to Newsvine!"),
			"live" => array(
				 "url" => "https://favorites.live.com/quickadd.aspx?".htmlentities("marklet=1&mkt=en-us&url=".$this->pageURL."&title=".$this->pageTitle."&top=1"),
				 "title" => "Add to Windows Live"),
			"myweb" => array(
				 "url" =>  "http://myweb.yahoo.com/myresults/bookmarklet?".htmlentities("t=".$this->pageTitle."&u=".$this->pageURL."&ei=UTF"),
				 "title" => "Add to Yahoo MyWeb"),
			"google" => array(
				 "url" =>  "http://www.google.com/bookmarks/mark?".htmlentities("op=edit&output=popup&bkmk=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Googlize this post!"),
			"stumbleupon" => array(
				 "url" => "http://www.stumbleupon.com/submit?".htmlentities("url=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Stumble It!"),
			"simpy" => array(
				 "url" => "http://simpy.com/simpy/LinkAdd.do?".htmlentities("title=".$this->pageTitle."&href=".$this->pageURL.""),
				 "title" => "Add to Simpy"),
			"favourites" => array(
				 "url" => "#",
				 "click" => "bookmark('".$this->pageURL."', '".$this->pageTitle."'); return false;",
				 "title" => "Add to favourites (Internet Explorer Only)"),
			"facebook" => array(
				 "url" => "http://www.facebook.com/share.php?".htmlentities("u=".$this->pageURL."&t=".$this->pageTitle.""),
				 "title" => "Share on Facebook"),
			"socialmarker" => array(
				 "url" => "http://www.socialmarker.com/?".htmlentities("link=".$this->pageURL."&title=".$this->pageTitle.""),
				 "title" => "Bookmark Elsewhere")
			);
		}
		return $bookmarks;
	}

}
