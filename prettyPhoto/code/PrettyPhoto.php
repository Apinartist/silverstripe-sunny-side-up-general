<?php

class PrettyPhoto extends Object {

	private static $themes = array("dark_rounded", "dark_square", "facebook", "light_rounded", "light_square");

	protected static $theme = "";
		static function set_theme($v) {if(!in_array($v, self::$themes)) {user_error("Please select a valid PrettyPhoto theme in PrettyPhoto::set_theme()", E_USER_WARNING);} self::$theme = $v;}

	protected static $more_config = "";
		static function set_more_config($v) {self::$more_config = $v;}

	static function include_code() {
		if(Director::is_ajax()) {
			self::block();
		}
		else {
			Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
			Requirements::javascript('prettyphoto/javascript/jquery.prettyPhoto.js');
			Requirements::css('prettyphoto/css/prettyPhoto.css');
			$config = '';
			if(self::$theme) {
				$config .= "theme: '".self::$theme."'";
			}
			if(self::$more_config) {
				$config .= ", " . self::$more_config;
			}
			Requirements::customScript('PrettyPhotoInitConfigs = {'.$config.'}; jQuery(document).ready(function(){PrettyPhotoLoader.load("body")});', "prettyPhotoCustomScript");
		}
	}

	static function block() {
		Requirements::block('prettyphoto/javascript/jquery.prettyPhoto.js');
		Requirements::block('prettyphoto/css/prettyPhoto.css');
		Requirements::block("prettyPhotoCustomScript");
	}
}