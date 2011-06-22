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
	
	protected $value, $min, $max, $ranges, $minorTicks, $majorTicks;
	
	static $range_colors = array('red', 'green', 'yellow');
	
	static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/gauge.js');
	}
	
	function __construct($value) {
		parent::__construct();
		$this->value = $value;
		self::addRequirements();
	}
	
	function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"gauge\"></div>";
	}
	
	function getJavascriptParams() {
		$params = array(
			'id' => "GC_$this->id",
			'title' => $this->title ? $this->title : '',
			'value' => $this->value
		);
		
		$options['width'] = $this->width ? $this->width : Chart::$default_width;
		$options['height'] = $this->height ? $this->height : Chart::$default_height;
		
		if($this->min) $options['min'] = $this->min;
		if($this->max) $options['max'] = $this->max;
		if($this->ranges) {
			foreach($this->ranges as $color => $range) {
				$options["{$color}From"] = $range[0];
				$options["{$color}To"] = $range[1];
			}
		}
		if($this->minorTicks) $options['minorTicks'] = $this->minorTicks;
		if($this->majorTicks) $options['majorTicks'] = $this->majorTicks;
		
		$params['options'] = $options;
		return $params;
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawGaugeChart($params);";
	}
	
	function setMin($min) {$this->min = $min;}
	function setMax($max) {$this->max = $max;}
	
	function setColorRange($color, $from, $to) {
		if(in_array($color, self::$range_colors)) $this->ranges[$color] = array($from, $to);
	}
	
	function setMinorTicks($number) {$this->minorTicks = $number;}
	function setMajorTicks(array $ticks) {$this->majorTicks = $ticks;}
}

?>