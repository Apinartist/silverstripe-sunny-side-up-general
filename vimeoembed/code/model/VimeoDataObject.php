<?php

class VimeoDataObject extends DataObjectDecorator {

	static $db = array(
		"URL" => "Text",
		"HTMLSnippet" => "HTMLText"
	);

	protected static $vimeo_base_url = "http://player.vimeo.com/";//The exact width of the video. Defaults to original size.
		static function set_vimeo_base_url($v){self::$vimeo_base_url = $v;}
		static function get_vimeo_base_url(){return self::$vimeo_base_url;}

	protected static $width = null;//The exact width of the video. Defaults to original size.
		static function set_width($v){self::$width = $v;}
		static function get_width(){return self::$width;}

	protected static $maxwidth = null;////Same as width, but video will not exceed original size.
		static function set_maxwidth($v){self::$maxwidth = $v;}
		static function get_maxwidth(){return self::$maxwidth;}

	protected static $height = null;//The exact height of the video. Defaults to original size.
		static function set_height($v){self::$height = $v;}
		static function get_height(){return self::$height;}

	protected static $maxheight = null;//Same as height, but video will not exceed original size.
		static function set_maxheight($v){self::$maxheight = $v;}
		static function get_maxheight(){return self::$maxheight;}

	protected static $byline = null;//Show the byline on the video. Defaults to true.
		static function set_byline($v){self::$byline = $v;}
		static function get_byline(){return self::$byline;}

0	protected static $title = null;//Show the title on the video. Defaults to true.
		static function set_title($v){self::$title = $v;}
		static function get_title(){return self::$title;}

	protected static $portrait = null;//// Show the user's portrait on the video. Defaults to true.
		static function set_portrait($v){self::$portrait = $v;}
		static function get_portrait(){return self::$portrait;}

	protected static $color = null;// Specify the color of the video controls.
		static function set_color($v){self::$color = $v;}
		static function get_color(){return self::$color;}

	protected static $callback = null;//When returning JSON, wrap in this function.
		static function set_callback($v){self::$callback = $v;}
		static function get_callback(){return self::$callback;}

	protected static $autoplay = null;//Automatically start playback of the video. Defaults to false.
		static function set_autoplay($v){self::$autoplay = $v;}
		static function get_autoplay(){return self::$autoplay;}

	protected static $xhtml = null;// Make the embed code XHTML compliant. Defaults to true.
		static function set_xhtml($v){self::$xhtml = $v;}
		static function get_xhtml(){return self::$xhtml;}

	protected static $api = null;// Enable the Javascript API for Moogaloop. Defaults to false.
		static function set_api($v){self::$api = $v;}
		static function get_api(){return self::$api;}

	protected static $wmode = null;//add the "wmode" parameter. Can be either transparent or opaque.
		static function set_wmode($v){self::$wmode = $v;}
		static function get_wmode(){return self::$wmode;}

	protected static $iframe;// Use our new embed code. Defaults to true. NEW!
		static function set_iframe($v){self::$iframe = $v;}
		static function get_iframe(){return self::$iframe;}

	protected $exclude_from_page_classes = array();
		static function set_exclude_from_page_classes($v){self::$exclude_from_page_classes = $v;}
		static function get_exclude_from_page_classes (){return self::$exclude_from_page_classes;}

	protected $include_in_page_classes = array();
		fstatic function set_include_in_page_classes($v){self::$include_in_page_classes = $v;}
		static function get_include_in_page_classes(){return self::$include_in_page_classes;}

	public function extraCMSFields(&$fields) {
		$fields = parent::getCMSFields();
		$addField = false;
		if(in_array($this->owner->ClassName, self::get_include_in_page_classes()) || !count(self::get_include_in_page_classes())) {
			$addField = true
		}
		if(in_array($this->owner->ClassName, self::get_exclude_from_page_classes())) {
			$addField = false;
		}
		if($addField) {
			$fields->addFieldToTab("Root.Content.Vimeo", new TextField("VimeoURL", _t("VIMEOPAGE.URLFIELD", "video code - e.g. 123454")));
		}
		return $fields;
	}


	function VimeoHTMLString() {
		if(!$this->HTMLSnippet || isset($_GET["flush"])) {
			if($code = $this->VimeoURL) {
				$get = array();
				if($width = VimeoDOD::get_width()) {$get[] = array("width" => $width);}
				if($max_width = VimeoDOD::get_max_width()) {$get[] = array("max_width"=>$max_width);}
				if($height = VimeoDOD::get_height()) {$get[] = array("height",$height);}
				if($maxheight = VimeoDOD::get_maxheight()) {$get[] = array("maxheight"=>$maxheight);}
				if($byline = VimeoDOD::get_byline()) {$get[] = array("byline"=>$byline);}
				if($title = VimeoDOD::get_title()) {$get[] = array("title"=>$title);}
				if($portrait = VimeoDOD::get_portrait()) {$get[] = array("portrait"=>$portrait);}
				if($color = VimeoDOD::get_color()) {$get[] = array("color"=>$color);}
				if($callback = VimeoDOD::get_callback()) {$get[] = array("callback"=>$callback);}
				if($autoplay = VimeoDOD::get_autoplay ()) {$get[] = array("autoplay"=>$autoplay);}
				if($xhtml = VimeoDOD::get_xhtml()) {$get[] = array("xhtml"=>$xhtml);}
				if($api = VimeoDOD::get_api()) {$get[] = array("api"=>$api);}
				if($wmode = VimeoDOD::get_wmode()) {$get[] = array("wmode"=>$wmode);}
				if($iframe = VimeoDOD::get_iframe()) {$get[] = array("iframe"=>$iframe);}
				$url = '';
				$url .= VimeoDOD::get_vimeo_base_url().$code;
				foreach($get as $key => $value) {
					$get[$key] = $key."=".$value;
				}
				url .= "?".implode("&", $value);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec();
				print_r($data);
				die();
				$this->HTMLSnippet = $data;
				$this->write();
			}
		}
	}
	return $this->HTMLSnippet;
}

