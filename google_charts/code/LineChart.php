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
	/**
	 * A list of valid line chart types.
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/line_charts.html
	 * @var array
	 */
	public static $types = array(
		'lc',					//	A line chart where data points are spaced evenly along the x-axis. Axis lines are shown by default
		'ls', 				//	Similar to lc charts, but by default does not display axis lines.
		'lxy'					//	Lets you specify both x- and y-coordinates for each point, rather just the y values.
	);
	
	/**
	 * The current chart type.  Must be one of the values in LineChart::$types
	 *
	 * @var string
	 */
	protected $type = 'lc';
	
	/**
	 * The lines to show on the graph
	 *
	 * @var array
	 */
	protected $lines = array();

	/**
	 * The factor to multiply y values by
	 *
	 * @var int|float
	 */
	protected $factor;
	
	/**
	 * The number of decimal places to round to when factoring y values
	 *
	 * @var int
	 */
	protected $round = false;
	
	/**
	 * Returns a URL to this chart image on Google Charts
	 * 
	 * @param array $params An optional set of default chart parameters (eg for setting params in subclasses).  Will be overwritten by properties set on this chart)
	 * @return string
	 */
	public function Link(array $params = null) {
		
		if(count($this->lines) > 0) {
			foreach($this->lines as $line) {
				if($this->type == 'lxy' && isset($line['x'])) {
					$coordinates[] = implode(',', $line['x']);
				}
				
				if($this->factor) {
					$y = array();
					foreach($line['y'] as $value) {
						$yVal = $this->factor * $value;
						if ($this->round !== false) {
							$yVal = round($yVal, $this->round);
						}
						$y[] = $yVal;
					}
					$coordinates[] = implode(',', $y);
				} else {
					$coordinates[] = implode(',', $line['y']);
				}
				if(isset($line['color'])) {
					$colors[] = (is_array($line['color']) ? implode('|', $line['color']) : $line['color']);
				} else if ($this->generateColor) {
					$colors[] = Chart::get_hexa_color();
				}
				
				if(isset($line['legend'])) {
					if(! is_array($line['legend'])) {
						$line['legend'] = array($line['legend']);
					}
					foreach($line['legend'] as $legend) {
						$legends[] = str_replace(' ', '+', $legend);
					}
				}
			}
			$params[Chart::$data_param] = 't:' . implode('|', $coordinates);
			if(isset($colors)) {
				$params[Chart::$color_param] = implode(',', $colors);
			}
			if(isset($legends)) {
				$params[Chart::$legend_labels_param] = implode('|', $legends);
			}
		}
		
		return parent::Link($params);
	}
	
	
	/**
	 * Adds a line to the chart
	 * 
	 * @param array $y An array of y coordinates
	 * @param array $x An array of x coordinates.  This is only used if the chart type is 'lxy'
	 * @param string $color The colour of the line in RRGGBB format
	 * @param string $legend The legend text for this line
	 */
	public function addLine(array $y, array $x = null, $color = null, $legend = null) {
		$line = array('y' => $y);
		if($x) {
			$line['x'] = $x;
		}
		if($color) {
			$line['color'] = $color;
		}
		if($legend) {
			$line['legend'] = $legend;
		}
		$this->lines[] = $line;
	}
	
	/**
	 * Adds scaling to the y axis.  All y axis values are multiplied by $factor, rounding to $round decimal places
	 * 
	 * @param int|float $factor The factor to multiply y values by
	 * @param int $round The number of decimal places to round to
	 */
	public function setFactor($factor, $round = false) {
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
	/**
	 * Extensions used by this class
	 *
	 * @var array
	 */
	public static $extensions = array('InteractiveChart', 'InteractiveChart_Axis');
	
	/**
	 * Valid curve types
	 *
	 * @var array
	 */
	public static $curve_types = array('function', 'none');
	
	/**
	 * An array of allowed legend position codes
	 *
	 * @var array
	 */
	public static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	
	/**
	 * The current curve type
	 *
	 * @var string
	 */
	protected $curveType;
	
	/**
	 * Whether the chart should attempt to fill in gap in the data
	 *
	 * @var bool
	 */
	protected $interpolateNulls;
	
	/**
	 * The width of data lines in px
	 *
	 * @var int
	 */
	protected $lineWidth;
	
	/**
	 * The diameter of data points in px
	 *
	 * @var int
	 */
	protected $pointSize;
	
	
	/**
	 * Includes the needed JS files via the Requirements system
	 */
	public static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/line.js');
		Requirements::javascript('googlecharts/javascript/tooltipfix.js');
	}
	
	/**
	 * 
	 */
	public function __construct() {
		parent::__construct();
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
		return "<div id=\"{$params['id']}\" class=\"line\"></div>";
	}
	
	/**
	 * Returns the JS function call that generates the chart in string format
	 * 
	 * @return string
	 */
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawLineChart_Interactive($params);";
	}
	
	/**
	 * Returns an array of parameters that will be passed to the JS chart.draw() function
	 * 
	 * @return array An associative array of parameters
	 */
	public function getJavascriptParams() {
		$params['id'] = "LI_$this->id";
		
		foreach($this->lines as $line) {
			$yTitles[] = $line['legend'];
			$values[] = array_map('floatval', $line['y']);
			if(isset($line['color'])) {
				$colors[] = $line['color'];
			}
		}
		
		$params['xTitles'] = array_map('strval', $this->axisLabels[0]);
		$params['yTitles'] = $yTitles;
		$params['values'] = $values;
		
		if(isset($colors)) {
			$options['colors'] = $colors;
		}
		
		if($this->curveType) {
			$options['curveType'] = $this->curveType;
		}
		
		if($this->interpolateNulls) {
			$options['interpolateNulls'] = 'true';
		}
		
		if(isset($this->lineWidth)) {
			$options['lineWidth'] = $this->lineWidth;
		}
		
		if(isset($this->pointSize)) {
			$options['pointSize'] = $this->pointSize;
		}
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		
		return $params;
	}
	

	/**
	 * Set this graph's curve type
	 * 
	 * @param string $type Must be in {@see $curve_types}
	 */
	function setCurveType($type) {
		if(in_array($type, self::$curve_types)) {
			$this->curveType = $type;
		}
	}
	
	/**
	 * Calling this function will set interpolateNulls to true. 
	 * InterpolateNulls will make Google Charts guess/fill in missing data points.  Leaving it off will show a gap in the line instead
	 */
	function interpolateNulls() {
		$this->interpolateNulls = true;
	}
	
	/**
	 * Set the width of data lines
	 * 
	 * @param int $width Line width in px
	 */
	function setLineWidth($width) {
		$this->lineWidth = $width;
	}
	
	/**
	 * Set the diameter of points on the lines
	 * 
	 * @param int $size Point diameter in px
	 */
	function setPointSize($size) {
		$this->pointSize = $size;
	}
}

