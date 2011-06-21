<?php

/**
 * This class is used to create an interactive word cloud chart as a line of words using javascript
 * 
 * @link http://visapi-gadgets.googlecode.com/svn/trunk/termcloud/doc.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class WordCloudChart extends Chart {
	
	protected $words;
	
	static function addRequirements() {
		Requirements::css('http://visapi-gadgets.googlecode.com/svn/trunk/termcloud/tc.css');
		Requirements::javascript('http://visapi-gadgets.googlecode.com/svn/trunk/termcloud/tc.js');
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/wordcloud.js');
	}
	
	function __construct(array $words) {
		parent::__construct();
		$this->words = $words;
		self::addRequirements();
	}
	
	function forTemplate() {
		$params = $this->getJavascriptParams();
		$width = $this->width ? $this->width : Chart::$default_width;
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"wordcloud\" style=\"width: {$width}px;\"></div>";
	}
	
	function getJavascriptParams() {
		return array(
			'id' => "WC_$this->id",
			'words' => array_keys($this->words),
			'fontsizes' => array_values($this->words)
		);
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawWordCloudChart($params);";
	}
}

/**
 * This class is used to create an interactive word cloud chart as a rotating sphere of words using javascript
 * 
 * @link http://code.google.com/p/word-cumulus-goog-vis/wiki/UserGuide
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class WordCloudChart_Rotating extends Chart {
	
	protected $words;
	protected $speed, $color, $hoverColor;
	
	static $font_max = 30;
	static $font_min = 7;
	
	static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('http://word-cumulus-goog-vis.googlecode.com/svn/trunk/wordcumulus.js');
		Requirements::javascript('http://word-cumulus-goog-vis.googlecode.com/svn/trunk/swfobject.js');
		Requirements::javascript('googlecharts/javascript/wordcloudrotating.js');
	}
	
	function __construct(array $words) {
		parent::__construct();
		$max = max($words);
		$min = min($words);
		$aFactor = (self::$font_max - self::$font_min) / ($max - $min);
		$bFactor = self::$font_max - $max * $aFactor;
		foreach($words as $word => $total) $words[$word] = round($total * $aFactor + $bFactor, 1);
		$this->words = $words;
		self::addRequirements();
	}
	
	function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"wordcloudrotating\"></div>";
	}
	
	function getJavascriptParams() {
		$params = array(
			'id' => "WCR_$this->id",
			'words' => array_keys($this->words),
			'fontsizes' => array_values($this->words)
		);
		
		$options['width'] = $this->width ? $this->width : Chart::$default_width;
		$options['height'] = $this->height ? $this->height : Chart::$default_height;
		
		if($this->speed) $options['speed'] = $this->speed;
		if($this->color) $options['text_color'] = $this->color;
		if($this->hoverColor) $options['hover_text_color'] = $this->hoverColor;
		
		$params['options'] = $options;
		return $params;
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawWordCloudChart_Rotating($params);";
	}
	
	function setSpeed($speed) {$this->speed = $speed;}
	function setColor($color) {$this->color = $color;}
	function setHoverColor($hoverColor) {$this->hoverColor = $hoverColor;}
}

?>