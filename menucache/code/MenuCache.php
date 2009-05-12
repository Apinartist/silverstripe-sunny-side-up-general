<?php

class MenuCache extends DataObjectDecorator {
	function extraDBFields(){
		return array(
			'db' =>  array('MenuCache' => 'HTMLText' )
		);
	}

	function CachedMenu() {
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





}

class MenuCache_controller extends Extension {

	static $allowed_actions = array("showcachedmenu");

	function showcachedmenu() {
		return $this->owner->renderWith("UsedToCreateMenuCache");
	}

}
