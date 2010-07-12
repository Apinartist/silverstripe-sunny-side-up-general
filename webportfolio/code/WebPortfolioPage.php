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

	public static $has_many = array(
		"PortfolioItem" => "PortfolioItem"
	);

	public function getCMSFields() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Portfolio",
			new HasManyComplexTableField(
				$controller = $this,
				$name = "PortfolioItem",
				$sourceClass = "PortfolioItem",
				$fieldList,
				$detailFormFields = null,
				$sourceFilter = "{$bt}PortfolioItem{$bt}.{$bt}ParentID{$bt} = ".$this->ID,
				$sourceSort = "",
				$sourceJoin = ""
			)
		);
		return $fields;
	}

	function canCreate() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return !DataObject::get("SiteTree", "{$bt}ClassName{$bt} = 'WebPortfolioPage'");
	}

	function canDelete() {
		return false;
	}


}

class WebPortfolioPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript("webportfolio/javascript/webportfolio.js");
		Requirements::themedCSS("WebPortfolioPage");
	}

}
