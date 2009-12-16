<?php


/**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 **/


class SiteMapPage extends Page {

	static $add_action = 'Site Map Page';

	static $icon = 'sitemappage/images/treeicons/SiteMapPage';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

}

class SiteMapPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript('sitemappage/javascript/SiteMapPage.js');
		Requirements::themedCSS('SiteMapPage');
	}


}

