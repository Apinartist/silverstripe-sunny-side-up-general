<?php

class VimeoDOD extends DataObjectDecorator {

	function extraStatics = array(
		"has_one" = array("VimeoDataObject" => "VimeoDataObject")
	);

	protected $exclude_from_page_classes = array();
		static function set_exclude_vimeo_from_page_classes($v){self::$exclude_vimeo_from_page_classes = $v;}
		static function get_exclude_vimeo_from_page_classes (){return self::$exclude_vimeo_from_page_classes;}

	protected $include_vimeo_in_page_classes = array();
		static function set_include_vimeo_in_page_classes($v){self::$include_in_vimeo_page_classes = $v;}
		static function get_include_vimeo_in_page_classes(){return self::$include_vimeo_in_page_classes;}

	public function extraCMSFields(&$fields) {
		$addField = false;
		if(in_array($this->owner->ClassName, self::get_include_vimeo_in_page_classes()) || !count(self::get_include_vimeo_in_page_classes())) {
			$addField = true
		}
		if(in_array($this->owner->ClassName, self::get_exclude_vimeo_from_page_classes())) {
			$addField = false;
		}
		if($addField) {
			$tab = _t("VIMEOPAGE.TAB", "Root.Content.Vimeo");
			$listObject = DataObject::get("VimeoDataObject");
			if($listObject) {
				$list = $listObject->toDropDownMap($index = 'ID', $titleField = 'URL', $emptyString = null, $sort = false);
				$fields->addFieldToTab($tab, new DropdownField("VimeoDataObjectID", _t("VIMEOPAGE.URLFIELD", "Video")));
			}
		}
		return $fields;
	}
}

class VimeoDOD_Controller extends Extension{

	function VimeoObject() {
		if($code = $this->owner->VimeoURL) {
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
			curl_close($ch);
			print_r($data);
			return $data;
		}
	}
}

