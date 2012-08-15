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
		Requirements::customScript($js);
	}

	protected $IDArray = array();

	function SelectedWebPortfolioItems(){
		$components = $this->getManyManyComponents('WebPortfolioItems');
		if($components && $components->count()) {
			$this->IDArray = $components->column("ID");
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

	function json(){
		$json = '
{
    "timeline":
    {
        "headline":"'.$this->Title.'",
        "type":"default",
        "text":"'.Convert::raw2json($this->Content).'"';
        //"asset": {
        //    "media":"http://yourdomain_or_socialmedialink_goes_here.jpg",
        //    "credit":"Credit Name Goes Here",
        //    "caption":"Caption text goes here"
        //},

		$data = $this->SelectedWebPortfolioItems();
		if($data) {
			foreach($data as $site) {
				$startDate = "2011,12,10";
				$endDate = "2011,12,10";
				$headline = "2011,12,10";
				$text = "2011,12,10";
        $json .= '
        "date": [
            {
                "startDate":"",
                "endDate":"2011,12,11",
                "headline":"Headline Goes Here",
                "text":"<p>Body text goes here, some HTML is OK</p>",
                "tag":"This is Optional",
                "asset": {
                    "media":"http://twitter.com/ArjunaSoriano/status/164181156147900416",
                    "thumbnail":"optional-32x32px.jpg",
                    "credit":"Credit Name Goes Here",
                    "caption":"Caption text goes here"
                }
            }
        ],
        ';
			}
		}
/*
        "era": [
            {
                "startDate":"2011,12,10",
                "endDate":"2011,12,11",
                "headline":"Headline Goes Here",
                "text":"<p>Body text goes here, some HTML is OK</p>",
                "tag":"This is Optional"
            }

        ]
        */
		$json . ='
    }
}';
		return $json;
	}


}
