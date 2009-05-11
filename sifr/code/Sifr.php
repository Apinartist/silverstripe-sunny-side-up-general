<?php
/*
	Scalable Inman Flash Replacement (sIFR) is an open source JavaScript and Macromedia Flash
	based technology initially developed by Shaun Inman and improved by Mike Davidson and Mark
	Wubben that enables the replacement of text elements on HTML web pages with Flash equivalents.

	Mike Davidsons website
	@see http://www.mikeindustries.com/sifr

	sIFR Wiki and help forum
	@see http://wiki.novemberborn.net/sifr

*/

class Sifr extends Object {

	static $hide_browser_text = true;

	static $b_fix_frag_id_bug = true;

	static $is_disabled = false;

	static $fonts = array();

	static $js = "";

	public function loadSifr() {
		$js = "
			if(typeof sIFR == 'function'){";
		if(self::$is_disabled == true) {
			$js .= 'sIFR.bIsDisabled = true;';
		}
		else {
			foreach(self::$fonts as $font) {
				$js .= '
					sIFR.replaceElement(named({';
				$i = 0;
				foreach($font as $key=>$attribute) {
					if($attribute) {
						if(is_array($attribute)) {
							$js .= ', sFlashVars: "';
							$j = 0;
							foreach($attribute as $name => $value) {
								if($value) {
									if($j) {
										$js .= '&';
									}
									$js .= $name.'='.$value;
									$j++;
								}
							}
							$js .= '"';
						}
						else {
							if($i) {
							 $js .= ', ';
							}
							$js .= $key.':"' . $attribute.'"';
						}
						$i++;
					}
				}
				$js .= "}));\n";
			}
		}
		$js .= "sIFR.setup();\n";
		$js .= "}";
		Requirements::themedCSS("sIFR-screen.css", 'screen, projection');
		Requirements::themedCSS("sIFR-print.css", 'print');
		Requirements::javascript("sifr/javascript/sifr.js");
		Requirements::javascript("sifr/javascript/sifr-addons.js");
		Requirements::customScript($js);
		self::$fonts = array();
	}
	/*


	*/
	static function replace_element($named = "sifr", $element = "", $font = "") {
		$theme = SSViewer::current_theme();
		if(!$theme || 1 == 1) {
			$theme = 'sifr';
		}
		else {
			$theme = 'themes/'.$theme;
		}
		$file = $theme.'/fonts/'.$font.'.swf';
		if(!file_exists("../".$file)) {
			return die("can not find: ".$file);
		}
		self::$fonts[$named] = array('sSelector' => $element,'sFlashSrc' => "http://localhost/massolutions/".$file,'named' => $named);
		self::set_colours_and_case($named);
		self::set_padding($named);
		self::set_position($named);
	}

	static function set_colours_and_case($named = "sifr", $sColor = "#000000", $sLinkColor = "#555555", $bBgColor = "#FFFFFF", $sHoverColor = "#999999", $sWmode = "transparent", $sCase = "") {
		$array = Array(
			'sColor' => $sColor,
			'sLinkColor' => $sLinkColor ,
			'bBgColor' => $bBgColor,
			'sHoverColor' => $sHoverColor,
			'sWmode' => $sWmode,//transparent or opaque
			'sCase' => $sCase  //lower or upper
		);
		self::$fonts[$named] = array_merge(self::$fonts[$named],$array);
	}

  static function set_padding($named = "sifr", $nPaddingTop = 0, $nPaddingBottom = 0, $nPaddingRight = 0, $nPaddingLeft = 0) {
		$array = Array(
			'nPaddingTop' => $nPaddingTop,
			'nPaddingBottom' => $nPaddingBottom ,
			'nPaddingRight' => $nPaddingRight ,
			'nPaddingLeft' => $nPaddingLeft ,
		);
		self::$fonts[$named] = array_merge(self::$fonts[$named],$array);
	}

	static function set_position($named = "sifr", $textalign = "left", $offsetLeft = 0, $offsetTop = 0) {
		$array = Array(
			Array(
				'textalign' => $textalign,
				'offsetLeft' => $offsetTop ,
				'offsetTop' => $offsetTop ,
			)
		);
		self::$fonts[$named] = array_merge(self::$fonts[$named],$array);
	}



	function is_disabled() {
		$state = self::$is_disabled;
		return ($state) ? true : false;
	}

	/*
		Disable the sIFR so that you can test without the new fancy fonts
		around for printing and for the 0.1% of people
	*/
	static function disable() {
		self::$is_disabled = true;
	}


	/*
		Determines whether the Fragment Identifier Bug will be fixed or not.
		This is a very peculiar bug in IE where for each Flash movie in the page the fragment identifier
		part of the URI (#foo) is repeated in the document title. It has been reported by Macromedia that
		this bug can be fixed by updating the VBScript engine the browser uses. However, for those users
		who still have the wrong engine sIFR contains a workaround.
	*/
	static function disable_fix_frag_id_bug() {
		self::$b_fix_frag_id_bug = false;
	}
	/*
		Determines whether sIFR will hide the text you want to replace or not.
	*/
	static function hide_browser_text() {
		self::$hide_browser_text = false;
	}

	function showFonts() {
		print_r(self::$fonts);
	}
}
