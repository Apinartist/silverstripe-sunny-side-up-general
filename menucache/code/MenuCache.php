<?php

class MenuCache extends DataObjectDecorator {
	function extraDBFields(){
		return array(
			'db' =>  array('MenuCache' => 'HTMLText' )
		);
	}

	function CachedMenu() {
		if(isset($_GET["flush"])) {
			$this->clearmenucache();
		}
		if(!$this->owner->MenuCache) {
			$response = Director::test($this->owner->URLSegment."/showcachedmenu/");
			if(is_object($response)) {
				$content = $response->getBody();
			}
			else {
				$content = $response . '';
			}
			$sql = 'Update SiteTree_Live Set MenuCache = "'.addslashes($content).'" WHERE `ID` = '.$this->owner->ID.' LIMIT 1';
			DB::query($sql);
			return $content;
		}
		else {
			return $this->owner->MenuCache;
		}
	}

	function clearmenucache () {
		$sql = 'Update SiteTree_Live Set MenuCache = ""';
		DB::query($sql);
	}

	function onBeforeWrite() {
		$this->clearmenucache();
		parent::onBeforeWrite();
	}


}

class MenuCache_controller extends Extension {

	static $allowed_actions = array("showcachedmenu");

	function showcachedmenu() {
		return $this->owner->renderWith("UsedToCreateMenuCache");
	}

}
