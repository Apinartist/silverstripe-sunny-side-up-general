<?php

/**
 * This class is used to create a bar/column chart as an image using google charts
 * 
 * @link http://code.google.com/apis/chart/image/docs/gallery/bar_charts.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class BarChart extends LineChart {
	/**
	 * A list of valid chart types.
	 * 
	 * @var array
	 */
	public static $types = array(
		'bhs', 				//	Horizontal bar chart with stacked bars
		'bvs', 				//	Vertical bar chart with stacked bars
		'bvo', 				//	Vertical bar chart in which bars are stacked in front of one another, rather than atop one another
		'bhg', 				//	Horizontal bar charts with grouped bars
		'bvg'		 			//	Vertical bar chart with grouped bars
	);
	
	/**
	 * The name/key of the bar width/spacing parameter
	 *
	 * @var string
	 */
	public static $bar_width_spacing_param = 'chbh';
	
	/**
	 * The current chart type.  Must be one of the values in BarChart::$types
	 *
	 * @var string
	 */
	protected $type = 'bvs';
	
	/**
	 * The width or spacing mode of the bars. Can be "a", "r" or a width in pixels.
	 *
	 * @var string|int 
	 */
	protected $barWidthScale;
	
	/**
	 * The space between bars.  Can be an integer in pixels or a float if set to relative sizing
	 *
	 * @var int|float
	 */
	protected $barSpace;
	
	/**
	 * The space between groups of bars (with grouped bar chart types).  Can be an integer in pixels or a float if set to relative sizing
	 *
	 * @var int|float
	 */
	protected $barGroupSpace;
	
	
	/**
	 * Returns a URL to this chart image on Google Charts
	 * 
	 * @param array $params An optional set of default chart parameters (eg for setting params in subclasses).  Will be overwritten by properties set on this chart)
	 * @return string
	 */
	public function Link(array $params = null) {
		
		if (count($this->lines) > 0) {
			if ($this->barWidthScale) {
				$scale = $this->barWidthScale;
				if ($this->barSpace) {
					$scale .= ",$this->barSpace";
				} else if ($this->barGroupSpace) {
					$scale .= ',1';
				}
				if ($this->barGroupSpace) {
					$scale .= ",$this->barGroupSpace";
				}
				$params[self::$bar_width_spacing_param] = $scale;
			}
			if ($this->markerType) {
				foreach ($this->lines as $i => $serie) {
					$markers[] = "$this->markerType,$this->markerColor,$i,-1,$this->markerSize";
				}
				$params[self::$marker_param] = implode('|', $markers);
				if (isset($this->axisRange[1])) {
					$params['chds'] = implode(',', $this->axisRange[1]);
				}
			}
		}
		
		return parent::Link($params);
	}
	
	/**
	 * "Uninherits" this function from the parent LineChart since it's replaced with {@see addSerie()}
	 */
	public function addLine() {
		user_error('You can not use the \'addLine\' function on a bar/column chart. Use the \'addSerie\' function instead.');
	}
	
	/**
	 * Adds a series of bars to the chart
	 * 
	 * @param array $y An array of values (one for each bar in the series)
	 * @param string $color The colour of the bars in RRGGBB format
	 * @param string $legend The legend text for this series
	 */
	public function addSerie(array $y, $color = null, $legend = null) {
		parent::addLine($y, null, $color, $legend);
	}
	

	//	http://code.google.com/apis/chart/image/docs/gallery/bar_charts.html#chbh
	
	/**
	 * Adds a series of bars to the chart
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/bar_charts.html#chbh
	 * @param string|int Can be "a", "r" or a width in pixels.  See the link for details
	 * @param int|float The space between bars.  Can be an integer in pixels or a float if set to relative sizing
	 * @param int|float The space between groups of bars (with grouped bar chart types).  Can be an integer in pixels or a float if set to relative sizing
	 */
	public function setBarWidthAndSpacing($widthScale, $barSpace = null, $barGroupSpace = null) {
		if($widthScale == 'a' || $widthScale == 'r' || is_numeric($widthScale)) {
			$this->barWidthScale = $widthScale;
			if($barSpace != null) {
				$this->barSpace = $barSpace;
			}
			if($barGroupSpace != null) {
				$this->barGroupSpace = $barGroupSpace;
			}
		}
	}
	
}

/**
 * This class is used to create an interactive bar/column chart using javascript
 *
 * @link http://code.google.com/apis/chart/interactive/docs/gallery/barchart.html
 * @link http://code.google.com/apis/chart/interactive/docs/gallery/columnchart.html
 * @see BarChart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class BarChart_Interactive extends BarChart {
	/**
	 * Extensions used by this class
	 *
	 * @var array
	 */
	public static $extensions = array('InteractiveChart', 'InteractiveChart_Axis');

	/**
	 * An array of allowed legend position codes
	 *
	 * @var array
	 */
	public static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	
	/**
	 * Includes the needed JS files via the Requirements system
	 */
	public static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/bar.js');
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
		return "<div id=\"{$params['id']}\" class=\"bar\"></div>";
	}
	
	/**
	 * Returns the JS function call that generates the chart in string format
	 * 
	 * @return string
	 */
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawBarChart_Interactive($params);";
	}
	
	/**
	 * Returns an array of parameters that will be passed to the JS chart.draw() function
	 * 
	 * @return array An associative array of parameters
	 */
	public function getJavascriptParams() {
		$params['id'] = "BA_$this->id";
		
		foreach($this->lines as $serie) {
			$yTitles[] = $serie['legend'];
			$values[] = array_map('floatval', $serie['y']);
			if (isset($serie['color'])) {
				$colors[] = $serie['color'];
			}
		}
		
		$params['xTitles'] = $this->axisLabels[0];
		$params['yTitles'] = $yTitles;
		$params['values'] = $values;
		$params['type'] = $this->getInteractiveType();
		
		if(isset($colors)) {
			$options['colors'] = $colors;
		}
		
		$options['isStacked'] = ($this->isStacked() ? 'true' : 'false');
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		return $params;
	}
	
	/**
	 * Tells us if the bars are horizontal or vertical
	 * 
	 * @return string Can be "bar" for horizontal charts or "column" for vertical charts
	 */
	public function getInteractiveType() {
		return $this->type[1] == 'h' ? 'Bar' : 'Column';
	}
	
	/**
	 * Returns true if bars are stacked on top of eachother, false if they're grouped side-by-side
	 * 
	 * @return bool
	 */
	public function isStacked() {
		return $this->type[2] != 'g';
	}
}

