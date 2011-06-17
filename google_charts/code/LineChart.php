<?php

/**
 * This class is used to create a line chart as an image using google charts
 * 
 * @link http://code.google.com/apis/chart/image/docs/gallery/line_charts.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class LineChart extends Chart {
	
	protected $type = 'lc';
	
	static $types = array('lc', 'ls', 'lxy');
	
	protected $lines = array();
	protected $factor;
	protected $round = false;
	
	function addLine(array $y, array $x = null, $color = null, $legend = null) {
		$line = array('y' => $y);
		if($x) $line['x'] = $x;
		if($color) $line['color'] = $color;
		if($legend) $line['legend'] = $legend;
		$this->lines[] = $line;
	}
	
	function Link(array $params = null) {
		if(count($this->lines) > 0) {
			foreach($this->lines as $line) {
				if($this->type == 'lxy' && isset($line['x'])) $coordinates[] = implode(',', $line['x']);
				if($this->factor) {
					$y = array();
					foreach($line['y'] as $value) {
						$yVal = $this->factor * $value;
						if($this->round !== false) $yVal = round($yVal, $this->round);
						$y[] = $yVal;
					}
					$coordinates[] = implode(',', $y);
				}
				else $coordinates[] = implode(',', $line['y']);
				if(isset($line['color'])) $colors[] = is_array($line['color']) ? implode('|', $line['color']) : $line['color'];
				else if($this->generateColor) $colors[] = Chart::get_hexa_color();
				if(isset($line['legend'])) {
					if(! is_array($line['legend'])) $line['legend'] = array($line['legend']);
					foreach($line['legend'] as $legend) $legends[] = str_replace(' ', '+', $legend);
				}
			}
			$params[Chart::$data_param] = 't:' . implode('|', $coordinates);
			if(isset($colors)) $params[Chart::$color_param] = implode(',', $colors);
			if(isset($legends)) $params[Chart::$legend_labels_param] = implode('|', $legends);
		}
		return parent::Link($params);
	}
	
	function setFactor($factor, $round = false) {
		$this->factor = $factor;
		$this->round = $round;
	}
}

/**
 * This class is used to create an interactive line chart using javascript
 *
 * @link http://code.google.com/apis/chart/interactive/docs/gallery/linechart.html
 * @see LineChart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class LineChart_Interactive extends LineChart {
	
	static $curve_types = array('function', 'none');
	static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	
	protected $curveType;
	protected $interpolateNulls;
	protected $lineWidth;
	protected $pointSize;
	
	static $extensions = array('InteractiveChart', 'InteractiveChart_Axis');
	
	static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/line.js');
		Requirements::javascript('googlecharts/javascript/tooltipfix.js');
	}
	
	function __construct() {
		parent::__construct();
		self::addRequirements();
	}
	
	function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"line\"></div>";
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawLineChart_Interactive($params);";
	}
	
	function getJavascriptParams() {
		$params['id'] = "LI_$this->id";
		
		foreach($this->lines as $line) {
			$yTitles[] = $line['legend'];
			$values[] = array_map('floatval', $line['y']);
			if(isset($line['color'])) $colors[] = $line['color'];
		}
		
		$params['xTitles'] = array_map('strval', $this->axisLabels[0]);
		$params['yTitles'] = $yTitles;
		$params['values'] = $values;
		
		if(isset($colors)) $options['colors'] = $colors;
		
		if($this->curveType) $options['curveType'] = $this->curveType;
		
		if($this->interpolateNulls) $options['interpolateNulls'] = 'true';
		
		if(isset($this->lineWidth)) $options['lineWidth'] = $this->lineWidth;
		
		if(isset($this->pointSize)) $options['pointSize'] = $this->pointSize;
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		return $params;
	}
	
	function setCurveType($type) {
		if(in_array($type, self::$curve_types)) $this->curveType = $type;
	}
	
	function interpolateNulls() {$this->interpolateNulls = true;}
	
	function setLineWidth($width) {$this->lineWidth = $width;}
	
	function setPointSize($size) {$this->pointSize = $size;}
}

?>