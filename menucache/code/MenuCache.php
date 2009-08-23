<?php

class MenuCache extends DataObjectDecorator {

	/**
	* fields are typicall header, menu, footer
	*/

	protected static $fields = array(
		0 => "Header",
		2 => "Menu",
		1 => "Footer",
	);

	static function set_fields($array) {
		self::$fields = $array;
	}

	protected static $class_names_to_cache = array();

	protected static $tables_to_clear = array("SiteTree", "SiteTree_Live", "SiteTree_versions");

	static function add_class_name_to_cache($className) {
		self::$class_names_to_cache[] = $className;
	}

	function extraDBFields(){
		$dbArray = array(
			"DoNotMenuCache" => "Boolean"
		);
		foreach(self::$fields as $key => $field) {
			$dbArray[$this->fieldMaker($key)]  => "HTMLText"
		}
		return array(
			'db' =>  array(
				$dbArray
			)
		);
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

	protected function fieldMaker($fieldNumber) {
		return "CachedSection".$fieldNumber;
	}

	function CachedSection($fieldNumber) {
		$fieldName = fieldMaker($fieldNumber);
		if(isset($_GET["flush"])) {
			$this->clearmenucache();
		}
		if(isset(self::$fields[$fieldNumber])) {
			user_error("$fieldName is not a field that can be cached", E_USER_NOTICE);
		}
		else {
			if(!$this->owner->$fieldName && !$this->owner->DoNotMenuCache) {
				$response = Director::test($this->owner->URLSegment."/showcachedfield/".$fieldNumber);
				if(is_object($response)) {
					$content = $response->getBody();
				}
				else {
					$content = $response . '';
				}
				$sql = 'Update `SiteTree_Live` Set `$fieldName` = "'.$this->compressAndPrepareHTML($content).'" WHERE `ID` = '.$this->owner->ID.' LIMIT 1';
				DB::query($sql);
				return $content;
			}
			else {
				return $this->owner->$fieldName;
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


	function onBeforeWrite() {
		$this->clearmenucache();
		parent::onBeforeWrite();
	}

	function clearmenucache () {
		foreach(self:$tables_to_clear as $table) {
			foreach(self::$fields as $field) {
				$sql = 'Update `'.$table.'` Set `'.$field.'` = ""';
				DB::query($sql);
			}
		}
		return array();
	}

}

class MenuCache_controller extends Extension {

	static $allowed_actions = array("showcachedfield","clearmenucache","showuncachedmenu");

	function showcachedfield($actions) {
		$fieldNumber = $actions["ID"];
		return $this->owner->renderWith('UsedToCreate'.$fieldNumber.'Cache');
	}

	function showuncachedmenu($actions) {
		$fieldNumber = $actions["ID"];
		$this->owner->clearmenucache();
		return $this->owner->renderWith('UsedToCreate'.$fieldNumber.'Cache');
	}


}
