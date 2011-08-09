<?php

/**
 * This class is used to create an interactive gauge chart using javascript
 * 
 * @link http://code.google.com/apis/visualization/documentation/gallery/gauge.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class GaugeChart extends Chart {
	/**
	 * A list of allowed colours for ranges.  A range will mark an area of the gauge as being in the red, for example
	 * 
	 * @var array
	 */
	public static $range_colors = array('red', 'green', 'yellow');

	/**
	 * The current value of the gauge
	 * 
	 * @var int
	 */
	protected $value;
	
	/**
	 * The minimum value shown.  Defaults to 0 if not set.
	 * 
	 * @var int
	 */
	protected $min;
	
	
	/**
	 * The maximum value on the gauge.  Defaults to 100 if not set.
	 * 
	 * @var int
	 */
	protected $max;
	
	/**
	 * A nested array of colour ranges in the format array('red' => array(80, 100));
	 * 
	 * @var array
	 */
	protected $ranges;
	
	
	/**
	 * How many minor tick sections should appear between each major tick
	 * If not set, defaults to two sections (ie one marker)
	 * 
	 * @var int
	 */
	protected $minorTicks;
	
	/**
	 * An array of labels of the major ticks (will create a tick for each label).  Includes the "empty" and "full" ticks.
	 * 
	 * @var array
	 */
	protected $majorTicks;
	
	
	/**
	 * Includes the needed JS files via the Requirements system
	 */
	public static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/gauge.js');
	}
	
	
	/**
	 * @param int $value The value to display
	 */
	public function __construct($value) {
		parent::__construct();
		$this->value = $value;
		self::addRequirements();
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
		return "<div id=\"{$params['id']}\" class=\"gauge\"></div>";
	}
	
	/**
	 * Returns an array of parameters that will be passed to the JS chart.draw() function
	 * 
	 * @return array An associative array of parameters
	 */
	public function getJavascriptParams() {
		$params = array(
			'id' => "GC_$this->id",
			'title' => $this->title ? $this->title : '',
			'value' => $this->value
		);
		
		$options['width'] = ($this->width ? $this->width : Chart::$default_width);
		$options['height'] = ($this->height ? $this->height : Chart::$default_height);
		
		if($this->min) {
			$options['min'] = $this->min;
		}
		if($this->max) {
			$options['max'] = $this->max;
		}
		if($this->ranges) {
			foreach($this->ranges as $color => $range) {
				$options["{$color}From"] = $range[0];
				$options["{$color}To"] = $range[1];
			}
		}
		if($this->minorTicks) {
			$options['minorTicks'] = $this->minorTicks;
		}
		if($this->majorTicks) {
			$options['majorTicks'] = $this->majorTicks;
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
		return "drawGaugeChart($params);";
	}
	
	/**
	 * Set the minimum value for the gauge.  Min and Max are used to scale the colour ranges nad the position of the pointer but the values will only be displayed on labels if $majorTicks is not set
	 * 
	 * @param int $min
	 */
	public function setMin($min) {
		$this->min = $min;
	}
	
	/**
	 * Set the maximum value for the gauge.  Min and Max are used to scale the colour ranges nad the position of the pointer but the values will only be displayed on labels if $majorTicks is not set
	 * 
	 * @param int $max
	 */
	public function setMax($max) {
		$this->max = $max;
	}
	
	/**
	 * Set the minimum value for the gauge.  Min and Max are used to scale the colour ranges nad the position of the pointer but the values will only be displayed on labels if $majorTicks is not set
	 * 
	 * @param string $colour Must be a value from {@see GaugeChart::$range_colors}
	 * @param int $from The value on the gauge that this colour range will start from
	 * @param int $to The value on the gauge that this colour range will go to
	 */
	public function setColorRange($color, $from, $to) {
		if(in_array($color, self::$range_colors)) {
			$this->ranges[$color] = array($from, $to);
		}
	}
	
	/**
	 * Set the number of minor tick sections that should appear between each major tick
	 * Sections are the gaps between ticks.  So for eg setting this to "2" will result in one tick mark (with a gap on either side), setting it to "3" will result in two tick marks etc
	 * 
	 * @param int $number
	 */
	public function setMinorTicks($number) {
		$this->minorTicks = $number;
	}
	
	/**
	 * Set the labels for all of the major ticks.  A tick will be created for each label.  Note that this includes the "empty" and "full" (first and last) ticks
	 * 
	 * @param array $ticks An array of labels of the major ticks.
	 */
	public function setMajorTicks(array $ticks) {
		$this->majorTicks = $ticks;
	}
}
