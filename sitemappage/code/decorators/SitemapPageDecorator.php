<?php

class SitemapPageDecorator extends SiteTreeDecorator {

	protected static $sitemap_classes_to_exclude = array("ErrorPage");
		static function set_sitemap_classes_to_exclude($v) {self::$sitemap_classes_to_exclude = $v;}
		static function get_sitemap_classes_to_exclude() {return self::$sitemap_classes_to_exclude;}

	protected static $sitemap_classes_to_include = array();
		static function set_sitemap_classes_to_include($v) {self::$sitemap_classes_to_include = $v;}
		static function get_sitemap_classes_to_include() {return self::$sitemap_classes_to_include;}

	function SiteMapPages() {
		$where = '1 = 1';
		$inc = self::get_sitemap_classes_to_include();
		$exc = self::get_sitemap_classes_to_exclude();
		if(is_array($inc) && count($inc)) {
			$where = "\"ClassName\" IN ('".implode("','", $inc)."')";
		}
		elseif(is_array($exc) && count($exc)) {
			$where = "\"ClassName\" NOT IN ('".implode("','", $exc)."')";
		}
		return DataObject::get("SiteTree", "\"ShowInMenus\" = 1 AND \"ShowInSearch\" = 1 AND \"ParentID\" = {$this->owner->ID} AND \"ClassName\" <> 'SiteMapPage' AND $where");
	}

}
