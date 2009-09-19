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
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Portfolio",
			new HasManyComplexTableField(
				$controller = $this,
				$name = "PortfolioItem",
				$sourceClass = "PortfolioItem",
				$fieldList,
				$detailFormFields = null,
				$sourceFilter = "`PortfolioItem`.`ParentID` = ".$this->ID,
				$sourceSort = "",
				$sourceJoin = ""
			);
		);
		return $fields;
	}

	function canCreate() {
		return !DataObject::get("SiteTree", '`ClassName` = "QuotePage"');
	}

	function canDelete() {
		return false;
	}


}

class WebPortfolioPage_Controller extends Page_Controller {


}