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

	protected $IDArray = array();
	protected $hasFilter = false;

	function show(){
		$this->hasFilter = true;
		$code = Convert::raw2sql($this->request->param("ID"));
		if($code) {
			$obj = DataObject::get_one("WebPortfolioWhatWeDidDescriptor", "\"Code\" = '$code'");
			$this->Title .= " - ".$obj->Name;
			$this->MetaTitle .= " - ".$obj->Name;
			if($obj) {
				$components = $obj->getManyManyComponents('WebPortfolioItem');
				if($components && $components->count()) {
					$this->IDArray = $components->column("ID");
				}
			}
		}
		return array();
	}

	function SelectedWebPortfolioItems(){
		if($this->hasFilter) {
			if(!is_array($this->IDArray)) {
				$this->IDArray = array(0 => 0);
			}
			if(!count($this->IDArray)) {
				$this->IDArray = array(0 => 0);
			}
			return DataObject::get("WebPortfolioItem", "\"WebPortfolioItem\".\"ID\" IN (".implode(",", $this->IDArray).")");
		}
		else {
			return $this->WebPortfolioItems();
		}
	}

	function HasFilter(){
		return $this->hasFilter;
	}

}
