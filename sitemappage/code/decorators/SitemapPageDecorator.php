<?php

class SitemapPageDecorator extends SiteTreeDecorator {

	function SiteMapPages() {
		return DataObject::get("SiteTree", '`ShowInMenus` = 1 AND `ShowInSearch` = 1 AND `ParentID` = '.$this->owner->ID.' AND ClassName <> "SiteMapPage"');
	}

}