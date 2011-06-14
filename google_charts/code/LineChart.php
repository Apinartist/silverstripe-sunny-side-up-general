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
				if(isset($line['color'])) $colors[] = $line['color'];
				else if($this->generateColor) $colors[] = Chart::get_hexa_color();
				if(isset($line['legend'])) $legend[] = str_replace(' ', '+', $line['legend']);
			}
			$params[Chart::$data_param] = 't:' . implode('|', $coordinates);
			if(isset($colors)) $params[Chart::$color_param] = implode(',', $colors);
			if(isset($legend)) $params[Chart::$legend_labels_param] = implode('|', $legend);
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
 * @todo Finish to add the hAxis and vAxis functions
 */
class LineChart_Interactive extends LineChart {
	
	static $axis_titles_positions = array('in', 'out', 'none');
	static $curve_types = array('function', 'none');
	static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	
	protected $axisTitlesPosition;
	protected $curveType;
	protected $gridlineColor;
	protected $interpolateNulls;
	protected $lineWidth;
	protected $pointSize;
	
	static $extensions = array('InteractiveChart');
	
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
	
	function getJavascriptParams() {
		$params['id'] = "LI_$this->id";
		
		foreach($this->lines as $line) {
			$yTitles[] = $line['legend'];
			$values[] = array_map('intval', $line['y']);
			if(isset($line['color'])) $colors[] = $line['color'];
		}
		
		$params['xTitles'] = $this->axisLabels[0];
		$params['yTitles'] = $yTitles;
		$params['values'] = $values;
		
		if($this->axisTitlesPosition) $options['axisTitlesPosition'] = $this->axisTitlesPosition;
		
		if(isset($colors)) $options['colors'] = $colors;
		
		if($this->curveType) $options['curveType'] = $this->curveType;
		
		if($this->gridlineColor) $options['gridlineColor'] = $this->gridlineColor;
		
		if($this->interpolateNulls) $options['interpolateNulls'] = 'true';
		
		if(isset($this->lineWidth)) $options['lineWidth'] = $this->lineWidth;
		
		if(isset($this->pointSize)) $options['pointSize'] = $this->pointSize;
		$options['pointSize'] = 4;
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		return $params;
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawLineChart_Interactive($params);";
	}
	
	function setAxisTitlesPosition($position) {
		if(in_array($position, self::$axis_titles_positions)) $this->axisTitlesPosition = $position;
	}
	
	function setCurveType($type) {
		if(in_array($type, self::$curve_types)) $this->curveType = $type;
	}
	
	function setGridlineColor($color) {$this->gridlineColor = $color;}
	
	function interpolateNulls() {$this->interpolateNulls = true;}
	
	function setLineWidth($width) {$this->lineWidth = $width;}
	
	function setPointSize($size) {$this->pointSize = $size;}
}

?>