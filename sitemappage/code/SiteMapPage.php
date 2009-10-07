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

}

class SiteMapPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript('sitemap/javascript/sitemap.js');
		Requirements::themedCSS('SiteMapPage');
	}


}

