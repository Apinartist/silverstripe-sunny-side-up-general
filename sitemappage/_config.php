<?php
/**
 * @author nicolaas [at] www.sunnysideup.co.nz
**/


//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START sitemappage MODULE ----------------===================
// ADD THE FOLLOWING FUNCTION TO YOUR PAGE CLASS (NOT PAGE_CONTROLLER!)
/*
	function SiteMapPages() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return DataObject::get("SiteTree", "{$bt}ShowInMenus{$bt} = 1 AND {$bt}ShowInSearch{$bt} = 1 AND {$bt}ParentID{$bt} = ".$this->ID." AND {$bt}ClassName{$bt} <> 'SiteMapPage'");
	}
*/
//===================---------------- END sitemappage MODULE ----------------===================
