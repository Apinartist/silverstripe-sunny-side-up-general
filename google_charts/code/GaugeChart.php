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
	}
	
	function __construct($value) {
		parent::__construct();
		$this->value = $value;
		self::addRequirements();
	}
	
	function forTemplate() {
		$id = "GC_$this->id";
		$params['id'] = $id;
		$params['title'] = $this->title ? $this->title : '';
		$params['value'] = $this->value;
		
		$options[] = 'width: ' . ($this->width ? $this->width : Chart::$default_width);
		$options[] = 'height: ' . ($this->height ? $this->height : Chart::$default_height);
		
		if($this->min) $options[] = "min: $this->min";
		if($this->max) $options[] = "max: $this->max";
		if($this->ranges) {
			foreach($this->ranges as $color => $range) {
				$options[] = "{$color}From: {$range[0]}";
				$options[] = "{$color}To: {$range[1]}";
			}
		}
		if($this->minorTicks) $options[] = "minorTicks: $this->minorTicks";
		if($this->majorTicks) $options[] = "majorTicks: ['" . implode("', '", $this->majorTicks) . "']";
		
		$params['options'] = implode(', ', $options);
		Requirements::javascriptTemplate('googlecharts/javascript/gauge.js', $params);
		return "<div id=\"$id\" class=\"gauge\"></div>";
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