<?php


/**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 **/


class SiteMapPage extends Page {

	static $add_action = 'Site Map Page';

	static $icon = 'sitemappage/images/treeicons/SiteMapPage';
	
	function canCreate() {
		return ! DataObject::get_one('SiteMapPage');
	}
}

class SiteMapPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript('sitemappage/javascript/SiteMapPage.js');
		Requirements::themedCSS('SiteMapPage');
	}
}

