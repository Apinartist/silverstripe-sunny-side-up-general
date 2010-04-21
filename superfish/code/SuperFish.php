<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 **/

class PrettyPhoto extends Object {


	protected static $config = "";
		static function set_config($v) {self::$config = $v;}
		static function get_config() {if(self::$config){ return self::$config;} else {return self::default_config();}}


	static function include_code() {
		if(Director::is_ajax()) {
			self::block();
		}
		else {
			Requirements::javascript('superfish/javascript/hoverIntent.js');
			Requirements::javascript('superfish/javascript/superfish.js');
			Requirements::ThemedCSS('superfish');
			Requirements::customScript('superfishconfig', self::get_config());
		}
	}

	static function block() {
		Requirements::block('superfish/javascript/hoverIntent.js');
		Requirements::block('superfish/javascript/superfish.js');
		Requirements::block('superfish/css/superfish.css');
		Requirements::block('superfishconfig');
	}

	protected static function default_config() {
		return <<<JS
SuperFish::set_config("
	$(document).ready(function() {
			$('ul#Nav').superfish({
					delay:       1000,                            // one second delay on mouseout
					animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation
					speed:       'fast',                          // faster animation speed
					autoArrows:  false,                           // disable generation of arrow mark-up
					dropShadows: false                            // disable drop shadows
			});
	});
");

JS
;
	}

}