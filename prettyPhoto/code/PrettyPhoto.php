<?php

class PrettyPhoto extends Object {

	static function include_code() {
		if(Director::is_ajax()) {
			self::block();
		}
		else {
			Requirements::javascript('prettyphoto/javascript/jquery.prettyPhoto.js');
			Requirements::css('prettyphoto/css/prettyPhoto.css');
		}
	}

	static function block() {
		Requirements::block('prettyphoto/javascript/jquery.prettyPhoto.js');
		Requirements::block('prettyphoto/css/prettyPhoto.css');
	}


}