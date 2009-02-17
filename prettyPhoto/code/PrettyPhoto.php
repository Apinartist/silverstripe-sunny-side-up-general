<?php

class PrettyPhoto extends Object {

	static function includeCode() {
		if(Director::is_ajax()) {
			self::block();
		}
		else {
			Requirements::javascript('prettyPhoto/javascript/jquery.prettyPhoto.js');
			Requirements::css('prettyPhoto/css/prettyPhoto.css');
		}
	}

	static function block() {
		Requirements::block('prettyPhoto/javascript/jquery.prettyPhoto.js');
		Requirements::block('prettyPhoto/css/prettyPhoto.css');
	}


}