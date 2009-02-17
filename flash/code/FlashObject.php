<?php
/*
	@ see http://code.google.com/p/swfobject/wiki/documentation
	@ see http://www.swffix.org/swfobject/generator/
*/

class FlashObject extends ViewableData  {

	static $UseDynamicInsert = true;

	static $Filename = "flashObject.swf";

	static $ID = "FlashObject";

	static $Width = 200;

	static $Height = 200;

	static $FlashVersion = "6.0.0";

	static $AlternativeContent = '<a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>';

	static $params = Array();

	static $externalFlashFile = '';

	public function CreateFlashObject($Title = '', $FlashFileID = '', $FlashFilename = '', $AlternativeContent = '', $Width = 0, $Height = 0, $FlashVersion = '', Array $ParamArray) {
		if(!$Title ) {$Title  = self::$Title ;}
			$Title = Convert::raw2js($Title);
		if(!$FlashFileID ) {$FlashFileID  = self::$FlashFileID ;}
			$FlashFileID = Convert::raw2js($FlashFileID);
		if(!$FlashFilename) {$FlashFilename = Convert::raw2js(self::$FlashFilename);}else {$FlashFilename = self::$Filename;}
			$FlashFilename = Convert::raw2js($FlashFilename);
		if(!$AlternativeContent) {$AlternativeContent = self::$AlternativeContent;}
		if(!$Width) {$Width = self::$Width;}
			$Width = intval($Width);
		if(!$Height) {$Height = self::$Height;}
			$Height = intval($Height);
		if(!$FlashVersion) {$FlashVersion = self::$FlashVersion;}
		if(!$ParamArray) {$ParamArray = self::$ParamArray;}
		$doSet = new DataObjectSet();
		if($FlashFilename) {
			$params = '';
			$paramsJS = '';
			foreach($ParamArray as $key=>$value) {
				$params .= '<param name="'.$key.'" value="'.$value.'" />';
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
				'UseDynamicInsert' => $UseDynamicInsert,
			);
			$doSet->push(new ArrayData($record));
			if(self::$UseDynamicInsert) {
				$js = '
					var flashvars = {};
					var params = {};
					'.$paramsJS.'
					var attributes = {};
					attributes.id = "'.self::$ID.'";
					setTimeout(
						function() {
							swfobject.embedSWF("'.$FlashFilename.'", "'.$FlashFileID.'", "'.$Width.'", "'.$Height.'", "'.$FlashVersion.'","flash/swfobject/expressInstall.swf", flashvars, params, attributes);
						}, 1000);';
			}
			else {
				$js = '
					jQuery(document).ready(
						function () {
							swfobject.registerObject("'.$FlashFileID.'", "'.$FlashVersion.'", "flash/swfobject/expressInstall.swf");
						}
					);';
			}
			Requirements::javascript("flash/javascript/swfobject.js");
			Requirements::customScript($js);

		}
		debug::show($doSet);
		die();
		return $doSet;
  }

	static function setUseDynamicInsert($value) {self::$UseDynamicInsert = $value;}
	static function setFilename($value) {self::$Filename = $value;}
	static function setDefaultID($value) {self::$ID = $value;}
	static function setDefaultWidth($value) {self::$Width = $value;}
	static function setDefaultHeight($value) {self::$Height = $value;}
	static function setDefaultFlashVersion($value) {self::$FlashVersion = $value;}
	static function setDefaultAlternativeContent($value) {self::$AlternativeContent = $value;}
	static function setDefaultExternalFlashFile($value) {self::$externalFlashFile = $value;}
	static function addParam($name, $value) {self::$params[$name] = $value;}
}



