<?php
/*
	Scalable Inman Flash Replacement (sIFR) is an open source JavaScript and Macromedia Flash
	based technology initially developed by Shaun Inman and improved by Mike Davidson and Mark
	Wubben that enables the replacement of text elements on HTML web pages with Flash equivalents.

	Mike Davidsons website
	@see http://www.mikeindustries.com/sifr

	sIFR Wiki and help forum
	@see http://wiki.novemberborn.net/sifr

*/

class Sifr extends Object {

	protected static $is_disabled = false;

	protected static $js_custom_file_location = "mysite/javascript/sifr.js";

	static function disable() {
		self::$is_disabled = true;
	}

	static function set_js_custom_file_location($folderAndFile) {
		self::$js_custom_file_location = $folderAndFile;
	}

	static function load_sifr() {
		if(!self::$is_disabled) {
			Requirements::css("sifr/css/sifr.css", 'screen, projection');
			Requirements::themedCSS("sifr-themed.css", 'screen, projection');
			Requirements::javascript("sifr/javascript/sifr.js");
			Requirements::javascript("sifr/javascript/sifr-debug.js");
			Requirements::javascript(self::$js_custom_file_location);
		}
	}

	function is_disabled() {
		$state = self::$is_disabled;
		return ($state) ? true : false;
	}


}
