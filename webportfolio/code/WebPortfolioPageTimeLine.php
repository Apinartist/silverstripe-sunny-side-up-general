<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package webportfolio
 * @sub-packages webportfolio
 *
 *
 *
 */

class WebPortfolioPageTimeLine extends Page {

	static $icon = "webportfolio/images/treeicons/WebPortfolioPageTimeLine";

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

class WebPortfolioPageTimeLine_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript("webportfolio/thirdparty/TimelineJS/compiled/js/timeline-embed.js");
		Requirements::javascript("webportfolio/javascript/timeline-executive.js");
		$js = <<<JS
		    var timeline_config = {
					width: 	"100%",
					height: "100%",
					source: 'webportfolio/thirdparty/timelineJS/examples/example_kitchensink.json',
					//start_at_end:	true,								//OPTIONAL
					//hash_bookmark: true,								//OPTIONAL
					css: 	'../compiled/css/timeline.css',				//OPTIONAL
					js: 	'../compiled/js/timeline-min.js'				//OPTIONAL
			}
JS;
		Requirements::customScript($js);
	}

	protected $IDArray = array();

	function SelectedWebPortfolioItems(){
		if($this->hasFilter) {

		}
		else {
			$components = $this->getManyManyComponents('WebPortfolioItems');
			if($components && $components->count()) {
				$this->IDArray = $components->column("ID");
			}
		}
		$reset = false;
		if(!$this->IDArray) {
			$reset = true;
		}
		elseif(!is_array($this->IDArray)) {
			$reset = true;
		}
		elseif(!count($this->IDArray)) {
			$reset = true;
		}
		if($reset) {
			$this->IDArray = array(0 => 0);
		}
		return DataObject::get(
			"WebPortfolioItem",
			"\"WebPortfolioItem\".\"ID\" IN (".implode(",", $this->IDArray).") AND \"WebPortfolioPage_WebPortfolioItems\".\"WebPortfolioPageID\" = ".$this->ID,
			"Favourites DESC, RAND()",
			" INNER JOIN \"WebPortfolioPage_WebPortfolioItems\" ON \"WebPortfolioPage_WebPortfolioItems\".\"WebPortfolioItemID\" = \"WebPortfolioItem\".\"ID\""
		);
	}


}
