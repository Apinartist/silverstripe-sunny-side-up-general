<?php


class ThickBox extends Object {

	static function includeCode() {
		Requirements::javascript('thickbox/javascript/jquery.thickbox.js');
		Requirements::css('thickbox/css/thickbox.css');
	}

	static function block() {
		Requirements::block('thickbox/javascript/jquery.thickbox.js');
		Requirements::block('thickbox/css/thickbox.css');
	}

	static function setAlternativeLoadingImage($location) {
		Requirements::customScript('var tb_pathToImage = "'.$location.'";');
	}

}