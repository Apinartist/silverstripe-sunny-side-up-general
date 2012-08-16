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

	public static $db = array(
		"TimeLineHeader" => "Varchar",
		"TimeLineIntro" => "HTMLText"
	);

	public static $many_many = array(
		"WebPortfolioItems" => "WebPortfolioItem"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Portfolio", new HTMLEditorField("TimeLineIntro", "Time line intro", 3));
		$fields->addFieldToTab("Root.Content.Portfolio", new TextField("TimeLineHeader", "Time line header"));
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
		Requirements::customScript("var timeline_config_source = '".$this->Link("json/")."';", "timeline_config_source");
		Requirements::javascript("webportfolio/javascript/timeline-executive.js");
		Requirements::javascript("webportfolio/thirdparty/TimelineJS/compiled/js/timeline-embed.js");
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
        "headline":'.$this->html2json($this->TimeLineHeader).',
        "type":"default",
        "text": '.$this->html2json($this->TimeLineIntro).',
        "date": [';
        //'.$this->html2json($this->TimeLineIntro).'';//
        //"asset": {
        //    "media":"http://yourdomain_or_socialmedialink_goes_here.jpg",
        //    "credit":"Credit Name Goes Here",
        //    "caption":"Caption text goes here"
        //},

		$data = $this->WebPortfolioItems();
		if($data && $data->count()) {
			$dayExistsArray = array();
			foreach($data as $site) {

				if($site->StartDate) {
					$startDateRaw = $site->StartDate;
				}
				else {
					$startDateRaw = $this->Created;
				}
				$startDateArray = explode("-", $startDateRaw);
				$startDate = intval($startDateArray[0]). ",".intval($startDateArray[1]). ",".intval($startDateArray[2]);
				$headLine = str_replace("https://www.", "", $site->getTitle());
				$headLine = str_replace("http://www.", "", $site->getTitle());
				$headLine = str_replace("https://", "", $headLine);
				$headLine = str_replace("http://", "", $headLine);
				$headLine = str_replace(".", " . ", $headLine);
				$headLine = $this->html2json($headLine); //
				$text = $this->html2json($site->renderWith("WebPortfolioPageOneItemTimeline")); // //
        $json .= '
            {
                "startDate":"'.$startDate.'",
                "headline": '.$headLine.',
                "text": '.$text.'
            }
        ';
        if(!$site->Last()) {
					$json .= ",";
				}
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
		$json .='
      ]
    }
}';
		return $json;
	}

	protected function html2json($html){
		if(!trim($html)) {
			$html = "&nbsp;";
		}
		$json = Convert::raw2json($html);
		$json = str_replace("\t", " ", $json);
		$json = str_replace("\r", " ", $json);
		$json = str_replace("\n", " ", $json);
		return $json;
	}

}
