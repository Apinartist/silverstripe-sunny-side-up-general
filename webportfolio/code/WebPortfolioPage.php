<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 *
 *
 *
 */

class WebPortfolioPage extends Page {

	static $icon = "webportfolio/images/treeicons/WebPortfolioPage";

	public static $db = array();

	public static $has_one = array();

	public static $many_many = array(
		"WebPortfolioItems" => "WebPortfolioItem"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Portfolio",
			new ManyManyComplexTableField(
				$controller = $this,
				$name = "WebPortfolioItems",
				$sourceClass = "WebPortfolioItem"
			)
		);
		return $fields;
	}


}

class WebPortfolioPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		PrettyPhoto::include_code();
		Requirements::javascript("webportfolio/javascript/webportfolio.js");
		Requirements::themedCSS("WebPortfolioPage");
	}

	function show(){

	}

}
