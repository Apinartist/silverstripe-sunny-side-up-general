<?php

class SitemapPageDecorator extends SiteTreeDecorator {

	protected static $sitemap_classes_to_exclude = array();
		static function set_sitemap_classes_to_exclude($v) {self::$sitemap_classes_to_exclude = $v;}
		static function get_sitemap_classes_to_exclude() {return self::$sitemap_classes_to_exclude;}

	protected static $sitemap_classes_to_include = array();
		static function set_sitemap_classes_to_include($v) {self::$sitemap_classes_to_include = $v;}
		static function get_sitemap_classes_to_include() {return self::$sitemap_classes_to_include;}

	function SiteMapPages() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$where = '1 = 1';
		$inc = self::get_sitemap_classes_to_include();
		$exc = self::get_sitemap_classes_to_include();
		if(is_array($inc) && count($inc)) {
			$where = "{$bt}ClassName{$bt} IN ('".implode("','", $inc)."')";
		}
		elseif(is_array($inc) && count($inc)) {
			$where = "{$bt}ClassName{$bt} NOT IN ('".implode("','", $inc)."')";
		}
		return DataObject::get("SiteTree", '{$bt}ShowInMenus{$bt} = 1 AND {$bt}ShowInSearch{$bt} = 1 AND {$bt}ParentID{$bt} = '.$this->owner->ID.' AND {$bt}ClassName{$bt} <> "SiteMapPage" AND '.$where);
	}

}
