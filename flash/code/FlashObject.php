<?php
/*
	@ see http://code.google.com/p/swfobject/wiki/documentation
	@ see http://www.swffix.org/swfobject/generator/
*/

class FlashObject extends ViewableData  {

	protected static $use_dynamic_insert = true;

	protected static $title = "MyFlashObjectTitle";

	protected static $filename = "flashObject.swf";

	protected static $flash_file_div_id = "FlashObject";

	protected static $width = 200;

	protected static $height = 200;

	protected static $flash_version = "6.0.0";

	protected static $alternative_content = '<a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>';

	protected static $param_array = Array();

	protected static $external_flash_file = '';

	public function CreateFlashObject($Title = '', $FlashFileDivID = '', $FlashFilename = '', $AlternativeContent = '', $Width = 0, $Height = 0, $FlashVersion = '', $ParamArray = array(), $YouTubeStyle = false) {
		if(!$Title ) {$Title  = self::$title ;}
		$Title = Convert::raw2js($Title);

		if(!$FlashFileDivID ) {$FlashFileDivID  = self::$flash_file_div_id ;}
		$FlashFileID = Convert::raw2js($FlashFileDivID);

		if(!$AlternativeContent) {$AlternativeContent = self::$alternative_content;}

		if(!$Width) {$Width = self::$width;}
		$Width = intval($Width);

		if(!$Height) {$Height = self::$height;}
		$Height = intval($Height);

		if(!$FlashVersion) {$FlashVersion = self::$flash_version;}

		if(!$ParamArray) {$ParamArray = self::$param_array;}

		$doSet = new DataObjectSet();


		if(!$FlashFilename) {$FlashFilename = self::$filename;}
		$FlashFilename = Convert::raw2js($FlashFilename);

		if($FlashFilename) {
			$params = '';
			$paramsJS = '';
			foreach($ParamArray as $key=>$value) {
				$params .= '<param name="'.$key.'" value="'.Convert::Raw2ATT($value).'" />';
				$paramsJS .= '
					params.'.$key.' = "'.$value.'";';
			}
			$record = array(
				'ID' => $FlashFileID ,
				'FileName' => $FlashFilename,
				'Title' => $Title,
				'Width' => intval($Width),
				'Height' => intval($Height),
				'FlashVersion' => $FlashVersion,
				'AlternativeContent' => $AlternativeContent,
				'Parameters' => $params,
				'UseDynamicInsert' => self::$use_dynamic_insert,
				'YouTubeStyle' => $YouTubeStyle
			);
			$doSet->push(new ArrayData($record));
			if(self::$use_dynamic_insert) {
				$js = '
					jQuery(document).ready(
						function () {
							jQuery(".FlashAlternativeContent").hide();
							var flashvars = {};
							var params = {};
							'.$paramsJS.'
							var attributes = {};
							attributes.id = "'.$FlashFileDivID.'";
							swfobject.embedSWF("'.$FlashFilename.'", "'.$FlashFileDivID.'", "'.$Width.'", "'.$Height.'", "'.$FlashVersion.'","flash/swfobject/expressInstall.swf", flashvars, params, attributes);
							jQuery(".FlashAlternativeContent").fadeIn(3000);
						}
					);';
			}
			else {
				$js = '
					jQuery(document).ready(
						function () {
							swfobject.registerObject("'.$FlashFileDivID.'", "'.$FlashVersion.'", "flash/swfobject/expressInstall.swf");
						}
					);';
			}
			if(!$YouTubeStyle) {
				Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
				Requirements::javascript("flash/javascript/swfobject.js");
				Requirements::customScript($js);
			}
		}
		return $doSet;
  }


	public function CreateYouTubeVideo($title, $code) {
		return $this->CreateFlashObject(
			$Title = $title,
			$FlashFileDivID = 'YouTubber',
			$FlashFilename = 'http://www.youtube.com/v/'.trim($code).'?fs=1&amp;hl=en_US',
			$AlternativeContent = '',
			$Width = 640,
			$Height = 385,
			$FlashVersion = '',
			$ParamArray = array(),
			$YouTubeStyle = true
		);
	}

	static function has_external_flash_file() {self::$external_flash_file ? true : false;}
	static function set_use_dynamic_insert($value) {self::$use_dynamic_insert = $value;}
	static function set_filename($value) {self::$filename = $value;}
	static function set_default_div_id($value) {self::$flash_file_div_id = $value;}
	static function set_default_width($value) {self::$width = $value;}
	static function set_default_height($value) {self::$height = $value;}
	static function set_default_flash_version($value) {self::$flash_version = $value;}
	static function set_default_alternative_content($value) {self::$alternative_content = $value;}
	static function set_default_external_flash_file($value) {self::$external_flash_file = $value;}
	static function add_param($name, $value) {self::$param_array[$name] = $value;}
}



