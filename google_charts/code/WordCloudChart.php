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
	
	public function __construct(array $words) {
		parent::__construct();
		$this->words = $words;
		self::addRequirements();
	}
	
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		$width = $this->width ? $this->width : Chart::$default_width;
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"wordcloud\" style=\"width: {$width}px;\"></div>";
	}
	
	public function getJavascriptParams() {
		return array(
			'id' => "WC_$this->id",
			'words' => array_keys($this->words),
			'fontsizes' => array_values($this->words)
		);
	}
	
	public function getJavascript() {
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
	
	public static $font_max = 30;
	public static $font_min = 7;	
	
	protected $words, $urls;
	protected $speed, $color, $hoverColor;
	
	
	static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('http://word-cumulus-goog-vis.googlecode.com/svn/trunk/wordcumulus.js');
		Requirements::javascript('http://word-cumulus-goog-vis.googlecode.com/svn/trunk/swfobject.js');
		Requirements::javascript('googlecharts/javascript/wordcloudrotating.js');
	}
	
	public function __construct(array $words) {
		parent::__construct();
		$max = max($words);
		$min = min($words);
		$aFactor = (self::$font_max - self::$font_min) / ($max - $min);
		$bFactor = self::$font_max - $max * $aFactor;
		foreach($words as $word => $total) $words[$word] = round($total * $aFactor + $bFactor, 1);
		$this->words = $words;
		self::addRequirements();
	}
	
	public function setURLS($urls) {
		$this->urls = $urls;
	}
	
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"wordcloudrotating\"></div>";
	}
	
	public function getJavascriptParams() {
		$params = array(
			'id' => "WCR_$this->id",
			'words' => array_keys($this->words),
			'fontsizes' => array_values($this->words)
		);
		
		$urls = array_fill(0, count($this->words), '');
		if($this->urls) {
			foreach($this->urls as $word => $url) {
				$index = array_search($word, $params['words']);
				if($index !== false) {
					$urls[$index] = $url;
				}
			}
		}
		$params['urls'] = $urls;
		
		$options['width'] = $this->width ? $this->width : Chart::$default_width;
		$options['height'] = $this->height ? $this->height : Chart::$default_height;
		
		if($this->speed) $options['speed'] = $this->speed;
		if($this->color) $options['text_color'] = $this->color;
		if($this->hoverColor) $options['hover_text_color'] = $this->hoverColor;
		
		$params['options'] = $options;
		return $params;
	}
	
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawWordCloudChart_Rotating($params);";
	}
	
	public function setSpeed($speed) {
		$this->speed = $speed;
	}
	
	public function setColor($color) {
		$this->color = $color;
	}
	
	public function setHoverColor($hoverColor) {
		$this->hoverColor = $hoverColor;
	}
	
}
