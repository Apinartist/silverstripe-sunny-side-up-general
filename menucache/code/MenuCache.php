<?php

class MenuCache extends DataObjectDecorator {

	function extraDBFields(){
		return array(
			'db' =>  array('MenuCache' => 'HTMLText' )
		);
	}

	protected static $class_names_to_cache = array();

	static function add_class_name_to_cache($className) {
		self::$class_names_to_cache[] = $className;
	}


	//------------------ static publisher
	/**
	 * Return a list of all the pages to cache
	 */
	function allPagesToCache() {
		// Get each page type to define its sub-urls
		$urls = array();
		// memory intensive depending on number of pages
		foreach(self::$class_names_to_cache as $className) {
			$pages = DataObject::get($className);
			if($pages) {
				foreach($pages as $page) {
					$urls = array_merge($urls, (array)$page->subPagesToCache());
				}
			}
		}
		// add any custom URLs which are not SiteTree instances
		//$urls[] = "sitemap.xml";
		return $urls;
	}

 /**
	 * Get a list of URLs to cache related to this page
	 */
	function subPagesToCache() {
		$urls = array();

		// add current page
		$urls[] = $this->Link();

		// cache the RSS feed if comments are enabled
		if ($this->ProvideComments) {
			$urls[] = Director::absoluteBaseURL() . "pagecomment/rss/" . $this->ID;
		}

		return $urls;
	}

	function pagesAffectedByChanges() {
		$urls = $this->subPagesToCache();
		if($p = $this->Parent) $urls = array_merge((array)$urls, (array)$p->subPagesToCache());
		return $urls;
	}



	//-------------------- menu cache
	function CachedMenu() {
		if(isset($_GET["flush"])) {
			$this->clearmenucache();
		}
		if(!$this->owner->MenuCache || Director::isDev()) {
			$response = Director::test($this->owner->URLSegment."/showcachedmenu/");
			if(is_object($response)) {
				$content = $response->getBody();
			}
			else {
				$content = $response . '';
			}
			$sql = 'Update SiteTree_Live Set MenuCache = "'.$this->compressAndPrepareHTML($content).'" WHERE `ID` = '.$this->owner->ID.' LIMIT 1';
			DB::query($sql);
			return $content;
		}
		else {
			return $this->owner->MenuCache;
		}
	}

	private function compressAndPrepareHTML($html) {
		$pat[0] = "/^\s+/";
		$pat[1] = "/\s{2,}/";
		$pat[2] = "/\s+\$/";
		$rep[0] = "";
		$rep[1] = " ";
		$rep[2] = "";
		$html = preg_replace($pat, $rep, $html);
		$html = trim($html);
		return addslashes($html);
	}


	function onBeforeWrite() {
		$this->clearmenucache();
		parent::onBeforeWrite();
	}

	function clearmenucache () {
		$sql = 'Update SiteTree_Live Set MenuCache = ""';
		DB::query($sql);
		return array();
	}

}

class MenuCache_controller extends Extension {

	static $allowed_actions = array("showcachedmenu","clearmenucache");

	function showcachedmenu() {
		return $this->owner->renderWith("UsedToCreateMenuCache");
	}


}
