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
	protected static $alternate_icons = array();

	/**
	* Include on all pages by default
	* @var boolean
	*/
	protected static $include_by_default = false;

	/**
	* Include on all pages (NO field required)
	* @var boolean
	*/

	protected static $share_this_all_in_one = false;

	protected static $always_include = true;

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

	var $encodedPageURL = '';
	var $nonEncodedPageURL = '';
	var $encodedPageTitle = '';
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
		if($this->ThisPageHasShareThis()){
			Requirements::css("sharethis/css/ShareThis.css");
			Requirements::javascript("sharethis/javascript/shareThis.js");
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

	/*
	 * returns array of bookmarks: array(array(name => array(url, title, click)))
	 *
	 */

	private function bookmarks() {
		$bookmarks = array();
		if($this->owner) {
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
				 "url" => "http://mystuff.ask.com/mysearch/BookmarkIt?".htmlentities("v=1.2&t=webpages&title=".$this->encodedPageTitle."&url=".$this->encodedPageURL."&abstext=".$this->encodedDescription),
				 "title" => "Share on Ask"),
			"bebo" => array(
				 "url" => "http://www.bebo.com/c/share?".htmlentities("Url=".$this->encodedPageURL."&Title=".$this->encodedPageTitle),
				 "title" => "Stumble It"),
			"blinklist" => array(
				 "url" => "http://blinklist.com/blink?".htmlentities("t=".$this->encodedPageTitle."&u=".$this->encodedPageURL."&d=".$this->encodedDescription),
				 "title" => "Share on BlinkList"),
			"blogmarks" => array(
				 "url" => "http://blogmarks.net/my/new.php?".htmlentities("mini=1&simple=1&url=".$this->encodedPageTitle."&title=".$this->encodedPageURL),
				 "title" => "BlogMark It"),
			"delicious" => array(
				 "url" => "http://delicious.com/save?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
				 "title" => "Add to Delicious"),
			"digg" => array(
				 "url" => "http://digg.com/submit?".htmlentities("url=".$this->nonEncodedURL."&title=".$this->encodedPageTitle),
				 "title" => "Digg this"),
			"dzone" => array(
				 "url" => "	 http://www.dzone.com/links/add.html?".htmlentities("url=".$this->nonEncodedURL."&title=".$this->encodedPageTitle),
				 "title" => "Add to DZone"),
			"facebook" => array(
				 "url" => "http://www.facebook.com/sharer.php?".htmlentities("u=".$this->encodedPageURL."&t=".$this->encodedPageTitle),
				 "title" => "Share on Facebook"),
			"fark" => array(
				 "url" => "	 http://cgi.fark.com/cgi/fark/submit.pl?".htmlentities("new_url=".$this->encodedPageURL),
				 "title" => "Fark It"),
			"friendfeed" => array(
				 "url" => "	 http://friendfeed.com/share/bookmarklet/frame#".htmlentities("title={$title.spaceencoded}".$this->encodedPageTitle."&url=".$this->encodedPageURL),
				 "title" => "Furl this"),
			"furl" => array(
				 "url" => "http://www.furl.net/storeIt.jsp?".htmlentities("t=".$this->encodedPageTitle."&u=".$this->encodedPageURL),
				 "title" => "Furl this"),
			"google" => array(
				 "url" =>  "http://www.google.com/bookmarks/mark?".htmlentities("op=edit&output=popup&bkmk=".$this->encodedPageURL."&title=".$this->encodedPageTitle."&annotation=".$this->encodedDescription),
				 "title" => "Googlize this post"),
			"kaboodle" => array(
				 "url" =>  "	 http://www.kaboodle.com/za/additem?".htmlentities("get=1&url=".$this->encodedPageURL."&title=".$this->encodedPageTitle);
				 "title" => "Share on Kadooble"),
			"linkedin" => array(
				 "url" =>  "http://www.linkedin.com/shareArticle?".htmlentities("mini=true&url=".$this->encodedPageURL."&title=".$this->encodedPageTitle."&source=".$this->encodedPageTitle),
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
				 "url" => "http://www.netvouz.com/action/submitBookmark?".htmlentities("url=".$this->encodedPageTitle.'&title='.$this->encodedPageTitle),
				 "title" => "Add to NetVouz"),
			"newsvine" => array(
				 "url" => "http://www.newsvine.com/_tools/seed".htmlentities("&save?u=".$this->encodedPageURL."&h=".$this->encodedPageTitle),
				 "title" => "Seed Newsvine"),
			"reddit" => array(
				 "url" => "http://reddit.com/submit?".htmlentities("url=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
				 "title" => "Reddit"),
			"simpy" => array(
				 "url" => "http://simpy.com/simpy/LinkAdd.do?".htmlentities("title=".$this->encodedPageTitle."&href=".$this->encodedPageURL),
				 "title" => "Add to Simpy"),
			"slashdot" => array(
				 "url" => "http://slashdot.org/bookmark.pl?".htmlentities("title=".$this->encodedPageTitle."&url=".$this->encodedPageURL),
				 "title" => "Slashdot It"),
			"spurl" => array(
				 "url" => "http://www.spurl.net/spurl.php?".htmlentities("title=".$this->encodedPageTitle."&url=".$this->encodedPageURL),
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
				 "url" =>  "	 http://bookmarks.yahoo.com/toolbar/savebm?u=".htmlentities("u=".$this->encodedPageTitle."&t=".$this->encodedPageURL),
				 "title" => "Bookmark it on Yahoo"),
			"socialmarker" => array(
				 "url" => "http://www.socialmarker.com/?".htmlentities("link=".$this->encodedPageURL."&title=".$this->encodedPageTitle),
				 "title" => "Bookmark Elsewhere")
			);
		}
		return $bookmarks;
	}

}
