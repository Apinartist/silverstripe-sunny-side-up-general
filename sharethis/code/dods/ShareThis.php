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
	* show bookmark title next to icon
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
	* specify icons to be included, if left empty, this variable will be ignored
	* @var array
	*/

	protected static $icons_to_include = array();
		static function set_icons_to_include($array) {self::check_array($array); self::$icons_to_include = $array;}

	/**
	* specify icons to be excluded, if left empty, this variable will be ignored
	* @var array
	*/

	protected static $icons_to_exclude = array();
		static function set_icons_to_exclude($array) {self::check_array($array); self::$icons_to_exclude = $array;}


	protected static function check_array($array) {
		if(!is_array($array)) {
			debug::show("parameter should be array");
		}
	}


	protected static function clean_boolean_value($value) {
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


	protected $bookmarks = array();

	protected $encodedPageURL = '';

	protected $nonEncodedPageURL = '';

	protected $encodedPageTitle = '';

	// %20 rather than +
	protected $encodedPageTitleSpaceEncoded = '';

	protected $pageIcon = '';

	function updateCMSFields(FieldSet &$fields) {
		if(!self::$always_include) {
			$fields->addFieldToTab("Root.Behaviour", new CheckboxField("ShareIcons","Show Share Icons on this page ?"));
			$fields->addFieldToTab("Root.Behaviour", new LiteralField("ShareIconsExplanation","<p>The social network <a href=\"/admin/social/\">icons shown</a> are editable within the CMS.</p>"));
		}
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

	public function ShareIconsKeys() {
		$keyData = array();
		$data = $this->makeBookmarks();
		foreach($data as $key => $ignore) {
			$keyData[$key] = $key;
		}
		return $keyData;
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
			$this->bookmarks = $this->makeBookmarks();
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
					}
					else {
						$itemArray["ImageSource"] = "sharethis/images/icons/".$key.".png";
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

	public function IncludeShareAll() {
		return self::$share_this_all_in_one;
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
		if($this->IncludeShareAll()) {
			if($this->makeBookmarkIndependentVariables()) {
				$this->pageIcon = urlencode(Director::absoluteBaseURL().'favicon.ico');
				return '
					<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#&amp;type=website"></script>
					<script type="text/javascript">
					SHARETHIS.addEntry(
						{
							title:"'.urldecode($this->encodedPageTitle).'",
							summary:"'.urldecode($this->encodedPageTitle).'",
							url:"'.urldecode($this->pageIcon).'",
							icon:"'.urldecode($this->encodedPageURL).'"
						},
						{button:true}
					);
					</script>';
			}
		}
	}

	/*
	 * returns array of bookmarks: array(array(name => array(url, title, click)))
	 * only ones that need to be included are returned
	 *
	 */

	protected function makeBookmarkIndependentVariables() {
		if($this->ThisPageHasShareThis()) {
			$this->nonEncodedPageURL = Director::absoluteBaseURL().$this->owner->URLSegment;
			$this->encodedPageURL = urlencode($this->nonEncodedPageURL);
			$this->encodedPageTitle = urlencode($this->owner->Title);
			$this->encodedPageTitleSpaceEncoded = str_replace("+", "%20",urlencode($this->owner->Title));
			if($this->owner->MetaDescription) {
				$this->encodedDescription = urlencode($this->owner->MetaDescription);
			}
			else {
				$this->encodedDescription = $this->encodedPageTitle;
			}
			return true;
		}
		else {
			debug::show("can not find owner, cant make shareicons!");
		}
	}

	protected function makeBookmarks() {
		if(!count($this->bookmarks) && $this->bookmarks !== 0) {
			if($this->makeBookmarkIndependentVariables()) {
				$bookmarks = array(
				"email" => array(
					 "url" => "mailto:?".htmlentities("Subject=".$this->encodedPageTitle."&Body=".$this->encodedDescription."%0D%0A".$this->encodedPageURL),
					 "title" => "Email"),
				"print" => array(
					 "url" => "#",
					 "click" => "window.print(); return false;",
					 "title" => "Print"),
				"favourites" => array(
					 "url" => "#",
					 "click" => "bookmark('".$this->encodedPageURL."', '".$this->encodedPageTitle."'); return false;",
					 "title" => "Add to favourites (Internet Explorer Only)"),
				"ask" => array(
					 "url" => "http://mystuff.ask.com/mysearch/BookmarkIt?".htmlentities("v=1.2&t=webpages&url=".$this->encodedPageURL."&title=".$this->encodedPageTitle."&abstext=".$this->encodedDescription),
					 "title" => "Share on Ask"),
				"bebo" => array(
					 "url" => "http://www.bebo.com/c/share?".htmlentities("Url=".$this->encodedPageURL."&Title=".$this->encodedPageTitle),
					 "title" => "Stumble It"),
				"blinklist" => array(
					 "url" => "http://blinklist.com/blink?".htmlentities("u=".$this->encodedPageURL."&t=".$this->encodedPageTitle."&d=".$this->encodedDescription),
					 "title" => "Share on BlinkList"),
				"blogmarks" => array(
					 "url" => "http://blogmarks.net/my/new.php?".htmlentities("mini=1&simple=1&url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "BlogMark It"),
				"delicious" => array(
					 "url" => "http://delicious.com/save?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Add to Delicious"),
				"digg" => array(
					 "url" => "http://digg.com/submit?".htmlentities("url=".$this->nonEncodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Digg this"),
				"dzone" => array(
					 "url" => "	 http://www.dzone.com/links/add.html?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Add to DZone"),
				"facebook" => array(
					 "url" => "http://www.facebook.com/sharer.php?".htmlentities("u=".$this->encodedPageURL."&t=".$this->encodedPageTitle),
					 "title" => "Share on Facebook"),
				"fark" => array(
					 "url" => "	 http://cgi.fark.com/cgi/fark/submit.pl?".htmlentities("new_url=".$this->encodedPageURL),
					 "title" => "Fark It"),
				"friendfeed" => array(
					 "url" => "	 http://friendfeed.com/share/bookmarklet/frame#".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitleSpaceEncoded),
					 "title" => "Furl this"),
				"furl" => array(
					 "url" => "http://www.furl.net/storeIt.jsp?".htmlentities("u=".$this->encodedPageURL."&t=".$this->encodedPageTitle),
					 "title" => "Furl this"),
				"google" => array(
					 "url" =>  "http://www.google.com/bookmarks/mark?".htmlentities("op=edit&output=popup&bkmk=".$this->encodedPageURL."&title=".$this->encodedPageTitle."&annotation=".$this->encodedDescription),
					 "title" => "Googlize this post"),
				"kaboodle" => array(
					 "url" =>  "http://www.kaboodle.com/za/additem?".htmlentities("get=1&url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Share on Kadooble"),
				"linkedin" => array(
					 "url" =>  "http://www.linkedin.com/shareArticle?".htmlentities("mini=true&url=".$this->encodedPageURL."&title=".$this->encodedPageTitle."&source=".Director::absoluteBaseURL()),
					 "title" => "Share on LinkedIn"),
				"live" => array(
					 "url" => "https://favorites.live.com/quickadd.aspx?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitleSpaceEncoded),
					 "title" => "Add to Windows Live"),
				"ma.gnolia" => array(
					 "url" => "http://ma.gnolia.com/bookmarklet/add?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Add to ma.gnolia"),
				"misterwong" => array(
					 "url" => "http://www.mister-wong.com/addurl/?".htmlentities("bm_url=".$this->encodedPageURL."&bm_description=".$this->encodedPageTitleSpaceEncoded),
					 "title" => "Wong It"),
				"myspace" => array(
					 "url" => "http://www.myspace.com/Modules/PostTo/Pages/?".htmlentities("u=".$this->encodedPageURL.'&t='.$this->encodedPageTitle),
					 "title" => "Share on MySpace"),
				"netvouz" => array(
					 "url" => "http://www.netvouz.com/action/submitBookmark?".htmlentities("url=".$this->encodedPageURL.'&title='.$this->encodedPageTitle),
					 "title" => "Add to NetVouz"),
				"newsvine" => array(
					 "url" => "http://www.newsvine.com/_tools/seed".htmlentities("&save?u=".$this->encodedPageURL."&h=".$this->encodedPageTitle),
					 "title" => "Seed Newsvine"),
				"reddit" => array(
					 "url" => "http://reddit.com/submit?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Reddit"),
				"simpy" => array(
					 "url" => "http://simpy.com/simpy/LinkAdd.do?".htmlentities("href=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Add to Simpy"),
				"slashdot" => array(
					 "url" => "http://slashdot.org/bookmark.pl?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Slashdot It"),
				"spurl" => array(
					 "url" => "http://www.spurl.net/spurl.php?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Spurl It"),
				"squidoo" => array(
					 "url" => "	 http://www.squidoo.com/lensmaster/bookmark?".htmlentities($this->encodedPageURL),
					 "title" => "Add to Squidoo"),
				"stumbleupon" => array(
					 "url" => "http://www.stumbleupon.com/submit?".htmlentities("url=".$this->nonEncodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Stumble It"),
				"stylehive" => array(
					 "url" => "http://www.stylehive.com/savebookmark/index.htm?".htmlentities("url=".$this->encodedPageURL),
					 "title" => "Add to Stylehive"),
				"technorati" => array(
					 "url" => "http://technorati.com/faves?".htmlentities("add=".$this->encodedPageURL),
					 "title" => "Add to Technorati"),
				"twitter" => array(
						"url" => "http://twitter.com/home?status=".htmlentities(urlencode("currently reading: ").$this->encodedPageURL),
						"title" => "Tweet It"),
				"yahoo" => array(
					 "url" =>  "http://bookmarks.yahoo.com/toolbar/savebm?u=".htmlentities("u=".$this->encodedPageURL."&t=".$this->encodedPageTitle),
					 "title" => "Bookmark it on Yahoo"),
				"socialmarker" => array(
					 "url" => "http://www.socialmarker.com/?".htmlentities("link=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
					 "title" => "Bookmark Elsewhere")
				);
				//which ones do we include
				$originalArray = array();
				foreach($bookmarks as $key => $ignore) {
					$originalArray[$key] = $key;
				}
				if(self::$use_data_object) {
					$objects = DataObject::get("ShareThisDataObject", "`Show` = 1");
					foreach($objects as $obj) {
						$this->bookmarks[$obj->Title] = $bookmarks[$$obj->Title];
					}
				}
				else {
					if(count(self::$icons_to_include)) {
						$new_array_of_icons_to_include = array();
						foreach(self::$icons_to_include as $key => $value) {
							$new_array_of_icons_to_include[$value] = $value;
							if(!isset($originalArray[$value])) {
								debug::show("Error in ShareIcons::set_icons_to_include, $key does not exist in bookmark list");
							}
						}
						foreach($bookmarks as $key => $array) {
							if(!isset($new_array_of_icons_to_include[$key])) {
								unset($bookmarks[$key]);
							}
						}
					}
					//which ones do we exclude
					if(count(self::$icons_to_exclude)) {
						foreach(self::$icons_to_exclude as $key) {
							if(!isset($originalArray[$key])) {
								debug::show("Error in ShareIcons::set_icons_to_exclude, $key does not exist in bookmark list");
							}
							else {
								unset($bookmarks[$key]);
							}
						}
					}
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
				$this->bookmarks = 0;
			}
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
