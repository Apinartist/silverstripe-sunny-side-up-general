<?php

class MenuCache extends DataObjectDecorator {
	function extraDBFields(){
		return array(
			'db' =>  array('MenuCache' => 'HTMLText' )
		);
	}

	function getMenuCache() {
		if(!$this->owner->MenuCache) [
			return "it works";
			$this->write();
			$page->writeToStage('Stage');
			$page->publish('Stage', 'Live');
		}
	}
	else {
		$this->owner->MenuCache;
	}
}

class MenuCache_controller extends Extension {


}
