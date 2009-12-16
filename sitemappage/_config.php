<?php
/**
 * @author nicolaas [at] www.sunnysideup.co.nz
**/


//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START sitemappage MODULE ----------------===================
// ADD THE FOLLOWING FUNCTION TO YOUR PAGE CLASS (NOT PAGE_CONTROLLER!)
/*
	function SiteMapPages() {
		return DataObject::get("SiteTree", '`ShowInMenus` = 1 AND `ShowInSearch` = 1 AND `ParentID` = '.$this->owner->ID.' AND `ClassName` <> "SiteMapPage"');
	}
*/
//===================---------------- END sitemappage MODULE ----------------===================
