<?php


 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 */


class SiteMapPage extends Page {

	static $add_action = 'Site Map Page';

	static $icon = 'mysite/images/treeicons/SiteMapPage';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	function SiteMapPages() {
		return DataObject::get("SiteMap", '`ShowInMenus` = 1 AND `ShowInSearch` = 1 AND `ParentID` = '.$this->ID.' AND ClassName <> "SiteMapPage"');
	}


}

class SiteMapPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript('sitemap/javascript/sitemap.js');
		Requirements::themedCSS('SiteMapPage');
	}


}

