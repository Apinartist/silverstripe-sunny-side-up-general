<?php

class SitemapPageDecorator extends SiteTreeDecorator {

	function SiteMapPages() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return DataObject::get("SiteTree", '{$bt}ShowInMenus{$bt} = 1 AND {$bt}ShowInSearch{$bt} = 1 AND {$bt}ParentID{$bt} = '.$this->owner->ID.' AND {$bt}ClassName{$bt} <> "SiteMapPage"');
	}

}
