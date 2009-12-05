<?php

class MenuCache extends DataObjectDecorator {

	protected static $class_names_to_cache = array();
	static function add_class_name_to_cache($className) {self::$class_names_to_cache[] = $className;}

	/**
	* fields are typicall header, menu, footer
	*/

	protected static $fields = array(
		0 => "Header",
		1 => "Menu",
		2 => "Footer",
		3 => "LayoutSection",
		4 => "other",
	);
	static function set_fields(array $array) {self::$fields = $array;}
	static function get_fields() {return self::$fields;}

	/* sets the cache number used for getting the "$Layout" of the individual page */
	protected static $layout_field = 3;
	static function set_layout_field($number) {self::$layout_field = $number;}
	static function get_layout_field() {return self::$layout_field;}

	protected static $tables_to_clear = array("SiteTree", "SiteTree_Live", "SiteTree_versions");
	static function get_tables_to_clear() {return self::$tables_to_clear;}

	function extraDBFields(){
		$dbArray = array(
			"DoNotCacheMenu" => "Boolean"
		);
		foreach(self::$fields as $key => $field) {
			$dbArray[self::field_maker($key)]  = "HTMLText";
		}
		return array(
			'db' =>  $dbArray
		);
	}

	public static function field_maker($fieldNumber) {
		return "CachedSection".$fieldNumber;
	}

	public static function fields_exists($number) {
		return (isset(self::$fields[$number]));
	}

	function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.Caching", new CheckboxField("DoNotCacheMenu","Do Not Cache Menu"));
		return $fields;
	}

	//------------------ static publisher ------------------ ------------------ ------------------ ------------------
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
		$urls[] = $this->owner->Link();

		// cache the RSS feed if comments are enabled
		if ($this->owner->ProvideComments) {
			$urls[] = Director::absoluteBaseURL() . "pagecomment/rss/" . $this->ID;
		}

		return $urls;
	}

	function pagesAffectedByChanges() {
		$urls = $this->subPagesToCache();
		if($p = $this->owner->Parent()) $urls = array_merge((array)$urls, (array)$p->subPagesToCache());
		return $urls;
	}


	//-------------------- menu cache ------------------ ------------------ ------------------ ------------------ ------------------ ------------------

	function clearfieldcache ($showoutput = false) {
		$fieldsToClear = array();
		foreach(self::get_fields() as $key => $field) {
			$fieldName = self::field_maker($key);
			$fieldsToClear[] = '`'.$fieldName.'` = ""';
		}
		if(count($fieldsToClear)) {
			foreach(self::get_tables_to_clear() as $table) {
				$msg = '';
				$sql = 'UPDATE `'.$table.'` SET '.implode(", ", $fieldsToClear);
				if($days = intval(Director::URLParam("ID"))) {
					$sql .= ' WHERE `LastEdited` > ( NOW() - INTERVAL '.$days.' DAY )';
					$msg .= ', created before the last '.$days.' days';
				}
				if($pageID = intval(Director::URLParam("OtherID"))) {
					$sql .= ' WHERE  `'.$table.'`.`ID` = '.$this->owner->ID.')';
					$msg .= ', for page with ID = '.$this->owner->ID.'';
				}
				if($showoutput) {
					Database::alteration_message("Deleting cached data from $table, ".$msg);
					debug::show($sql);
				}
				DB::query($sql);
			}
		}
		return array();
	}


	function onBeforeWrite() {
		//$this->clearfieldcache(); // technically this should be done, but it puts a lot of strain on saving so instead we encourage people to use ?flush=1
		parent::onBeforeWrite();
	}


}

class MenuCache_controller extends Extension {

	static $allowed_actions = array("showcachedfield","clearfieldcache","showuncachedfield", "clearallfieldcaches");

	protected function getHtml($fieldNumber) {
		if(MenuCache::get_layout_field() == $fieldNumber) {
			$className = $this->owner->ClassName;
			if("Page" == $className) {
				$className = "PageCached";
			}
			return $this->owner->renderWith(array($className, "PageCached"));
		}
		else {
			return $this->owner->renderWith('UsedToCreateCache'.$fieldNumber);
		}
	}

	function CachedField($fieldNumber) {
		$fieldName = MenuCache::field_maker($fieldNumber);
		if(isset($_REQUEST["flush"])) {
			$this->owner->clearfieldcache();
		}
		if(!(MenuCache::fields_exists($fieldNumber))) {
			user_error("$fieldName is not a field that can be cached", E_USER_ERROR);
		}
		else {
			if(!$this->owner->$fieldName || $this->owner->DoNotCacheMenu) {
				$fieldID = $fieldNumber;
				if($this->owner->URLSegment == "Security") {
					return '';
				}
				$content = $this->getHtml($fieldNumber);
				$sql = 'Update `SiteTree_Live` Set `'.$fieldName.'` = "'.$this->compressAndPrepareHTML($content).'" WHERE `ID` = '.$this->owner->ID.' LIMIT 1';
				DB::query($sql);
				return "<!-- will be cached next time as $fieldName START -->".$content."<!-- will be cached next time as  $fieldName END -->";
			}
			else {
				return "<!-- from cached field: $fieldName START -->".$this->owner->$fieldName."<!-- from cached field: $fieldName END -->";
			}
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



	function showcachedfield($httpRequest = null) {
		$fieldNumber = $httpRequest->param("ID");
		return $this->getHtml($fieldNumber);
	}

	function showuncachedfield($httpRequest = null) {
		$this->owner->clearfieldcache();
		return $this->showcachedfield($httpRequest);
	}

	function clearallfieldcaches($httpRequest = null) {
		$this->owner->clearfieldcache(true);
		return 'fields have been cleared, <a href="/">click to continue...</a>';
	}


}
