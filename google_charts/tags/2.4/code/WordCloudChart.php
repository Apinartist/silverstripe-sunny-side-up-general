<?php

/**
 * This class is used to create an interactive word cloud chart as a line of words using javascript
 * 
 * @link http://visapi-gadgets.googlecode.com/svn/trunk/termcloud/doc.html
 * @see Chart
 * @todo Currently links all words to "".  Need to implement passing in URLs or see if there's a way to turn off empty links
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class WordCloudChart extends Chart {
	/**
	 * The array of words to display on the chart
	 * 
	 * @var array
	 */
	protected $words;

	/**
	 * Includes the needed JS files via the Requirements system
	 */
	static function addRequirements() {
		$protocol = Director::protocol();
		Requirements::css("{$protocol}visapi-gadgets.googlecode.com/svn/trunk/termcloud/tc.css");
		Requirements::javascript("{$protocol}visapi-gadgets.googlecode.com/svn/trunk/termcloud/tc.js");
		Requirements::javascript("{$protocol}www.google.com/jsapi");
		Requirements::javascript('googlecharts/javascript/wordcloud.js');
	}
	
	/**
	 * Takes an array of words, with the word as the key and the frequency as the value.
	 * Font sizes and colours are determined by relative frequencies.
	 * 
	 * @param array $words An array of words to displayin the format array('myword' => 12);
	 */
	public function __construct(array $words) {
		parent::__construct();
		$this->words = $words;
		self::addRequirements();
	}
	
	/**
	 * Standard SS method
	 * 
	 * @return string A <div> tag and the javascript needed to convert it to a graph
	 */
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		$width = ($this->width ? $this->width : Chart::$default_width);
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"wordcloud\" style=\"width: {$width}px;\"></div>";
	}
	
	/**
	 * Returns an array of parameters that will be passed to the JS chart.draw() function
	 * 
	 * @return array An associative array of parameters
	 */
	public function getJavascriptParams() {
		return array(
			'id' => "WC_$this->id",
			'words' => array_keys($this->words),
			'fontsizes' => array_values($this->words)
		);
	}
	
	/**
	 * Returns the JS function call that generates the chart in string format
	 * 
	 * @return string
	 */
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
	
	/**
	 * The minimum font size for the cloud
	 * 
	 * @var int
	 */
	public static $font_min = 7;
	
	/**
	 * The maximum font size for the cloud
	 * 
	 * @var int
	 */
	public static $font_max = 30;
	
	/**
	 * The array of words to display on the chart
	 * 
	 * @var array
	 */
	protected $words;
	
	/**
	 * The array of URLs that the correspondings words in $words will link to
	 * 
	 * @var array
	 */
	protected $urls;
	
	
	protected $speed, $color, $hoverColor;
	
	/**
	 * Includes the needed JS files via the Requirements system
	 */
	static function addRequirements() {
		$protocol = Director::protocol();
		Requirements::javascript("{$protocol}www.google.com/jsapi");
		Requirements::javascript("{$protocol}word-cumulus-goog-vis.googlecode.com/svn/trunk/wordcumulus.js");
		Requirements::javascript("{$protocol}word-cumulus-goog-vis.googlecode.com/svn/trunk/swfobject.js");
		Requirements::javascript('googlecharts/javascript/wordcloudrotating.js');
	}
	
	/**
	 * Takes an array of words, with the word as the key and the frequency as the value.
	 * Font sizes and colours are determined by relative frequencies.
	 * 
	 * @param array $words An array of words to displayin the format array('myword' => 12);
	 */
	public function __construct(array $words) {
		parent::__construct();
		$max = max($words);
		$min = min($words);
		
		$difference = $max - $min;
		if ($difference < 1) {
			$difference = 1;
		}
		
		$aFactor = (self::$font_max - self::$font_min) / $difference;
		$bFactor = self::$font_max - $max * $aFactor;
		foreach($words as $word => $total) {
			$words[$word] = round($total * $aFactor + $bFactor, 1);
		}
		$this->words = $words;
		self::addRequirements();
	}
	
	/**
	 * Set the URLs that each word in the cloud will link to.
	 * 
	 * @param array $words An array of words to display in the format array('myword' => 'http://www.example.com');
	 */
	public function setURLS($urls) {
		$this->urls = $urls;
	}
	
	/**
	 * Standard SS method
	 * 
	 * @return string A <div> tag and the javascript needed to convert it to a graph
	 */
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"wordcloudrotating\"></div>";
	}
	
	/**
	 * Returns an array of parameters that will be passed to the JS chart.draw() function
	 * 
	 * @return array An associative array of parameters
	 */
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
		
		$options['width'] = ($this->width ? $this->width : Chart::$default_width);
		$options['height'] = ($this->height ? $this->height : Chart::$default_height);
		
		if($this->speed) {
			$options['speed'] = $this->speed;
		}
		
		if($this->color) {
			$options['text_color'] = $this->color;
		}
		
		if($this->hoverColor) {
			$options['hover_text_color'] = $this->hoverColor;
		}
		
		$params['options'] = $options;
		return $params;
	}
	
	/**
	 * Returns the JS function call that generates the chart in string format
	 * 
	 * @return string
	 */
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawWordCloudChart_Rotating($params);";
	}
	
	/**
	 * Set the speed the cloud rotates at.  Default is 100, 
	 * 
	 * @param int
	 */
	public function setSpeed($speed) {
		$this->speed = $speed;
	}
	
	/**
	 * Set the text colour in #RRGGBB format
	 * 
	 * @param string
	 */
	public function setColor($color) {
		$this->color = $color;
	}
	
	/**
	 * Set the hover text colour in #RRGGBB format
	 * NOTE: this currently does not work {@link http://code.google.com/p/word-cumulus-goog-vis/issues/detail?id=2}
	 * 
	 * @param string
	 */
	public function setHoverColor($hoverColor) {
		$this->hoverColor = $hoverColor;
	}
	
}
