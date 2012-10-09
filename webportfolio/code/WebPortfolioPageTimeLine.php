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
		"TimeLineIntro" => "HTMLText",
		"JSON" => "Text"
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

	function createJSON(){
		SSViewer::set_source_file_comments(false);
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
				$headLine = $this->html2json($headLine); /
				$site->HeadLine = $headLine;
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
		if($json) {
			$this->JSON = $json;
			$this->writeToStage('Stage');
			$this->Publish('Stage', 'Live');
			$this->Status = "Published";
			$this->flushCache();
		}
		return $json;
	}

	protected function html2json($html){
		$html = preg_replace('!\s+!', ' ', $html);
		if(!trim($html)) {
			$html = "&nbsp;";
		}
		$json = Convert::raw2json($html);
		return $json;
	}


}

class WebPortfolioPageTimeLine_Controller extends Page_Controller {


	protected static $ajax_file_location = "webportfolio/javascript/timeline-executive.js";
		static function set_ajax_file_location($s){self::$ajax_file_location = $s;}
		static function get_ajax_file_location(){return self::$ajax_file_location;}

	function init() {
		parent::init();
		Requirements::javascript(self::get_ajax_file_location());
		Requirements::javascript("webportfolio/thirdparty/TimelineJS/compiled/js/timeline-embed.js");
		Requirements::themedCSS("WebPortfolioPageTimeLine");
	}

	function json($request){
		SSViewer::set_source_file_comments(false);
		if(isset($_GET['flush']) || !$this->JSON) {
			return $this->createJSON();
		}
		return $this->JSON;
	}

	function JSONLink(){
		return Director::absoluteURL($this->Link("json/"));
	}

}
