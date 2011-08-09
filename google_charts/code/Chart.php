<?php

/**
 * This class is used as a parent class only to create an image chart using google charts
 * 
 * @link http://code.google.com/apis/chart/
 * @link http://code.google.com/apis/visualization/documentation/gallery.html
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
 
class Chart extends ViewableData {
	
	/**
	 * The base URL for generating links to charts
	 * 
	 * @var string
	 */
	protected static $base_url = 'http://chart.apis.google.com/chart?';
	
	/**
	 * The default width of generated charts in pixels.  Can be set with Chart::$default_width = 400; in your _config.php
	 * 
	 * @var int
	 */
	public static $default_width = 250;
	
	/**
	 * The default height of generated charts in pixels.  Can be set with Chart::$default_height = 400; in your _config.php
	 * 
	 * @var int
	 */
	public static $default_height = 100;
	
	/**
	 * The maximum area (in pixels) of the graph.  If (height * width) > $pixels_max, width is reduced
	 * 
	 * @todo find out the purpose behind this (is only used in MyBarChart and MyLineChart
	 * @todo add to _config.php examples
	 * @var int
	 */
	public static $pixels_max = 300000;
	
	/**
	 * Counts the number of chart instances so each can be assigned a unique numeric ID
	 * 
	 * @todo should this be protected instead of public?
	 * @var int
	 */
	public static $cpt = 0;
	
	/**
	 * Stores a list of valid chart types.  Overwritten in subclasses that set their own chart types.
	 * 
	 * @var array
	 */
	public static $types = array();
	
	
	/**
	 * The name/key of the chart type parameter to pass to Google Charts
	 * 	 
	 * @link http://code.google.com/apis/chart/image/docs/chart_params.html List of Google Charts parameters
	 * @var string
	 */
	public static $type_param = 'cht';
	
	/**
	 * The name/key of the chart data string parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $data_param = 'chd';
	
	/**
	 * The name/key of the chart size parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $size_param = 'chs';
	
	/**
	 * The name/key of the chart colour parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $color_param = 'chco';
	
	/**
	 * The name/key of the chart title parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $title_param = 'chtt';
	
	/**
	 * The name/key of the chart style parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $title_style_param = 'chts';
	
	/**
	 * The name/key of the chart data scaling parameter to pass to Google Charts
	 *
	 * @todo doesn't seem to be used anywhere
	 * @var string
	 */
	public static $data_scaling_param = 'chds';
	
	/**
	 * The name/key of the chart legend labels parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $legend_labels_param = 'chdl';
	
	/**
	 * The name/key of the chart legend position parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $legend_position_param = 'chdlp';
	
	/**
	 * The name/key of the chart legend style parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $legend_style_param = 'chdls';
	
	/**
	 * The name/key of the chart margins parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $margin_param = 'chma';
	
	/**
	 * The name/key of the chart visible axis list parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $visible_axes_param = 'chxt';
	
	/**
	 * The name/key of the chart axis range parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $axis_range_param = 'chxr';
	
	/**
	 * The name/key of the chart axis labels parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $axis_labels_param = 'chxl';
	
	/**
	 * The name/key of the chart background fill parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $background_fill_param = 'chf';
	
	/**
	 * The name/key of the chart marker parameter to pass to Google Charts
	 *
	 * @var string
	 */
	public static $marker_param = 'chm';
	
	
	/**
	 * An array of allowed legend position codes
	 *
	 * @var array
	 */
	public static $legend_positions = array(
		'b', 				//	Legend at the bottom of the chart, legend entries in a horizontal row.
		'bv', 			//	Legend at the bottom of the chart, legend entries in a vertical column.
		't', 				//	Legend at the top of the chart, legend entries in a horizontal row.
		'tv', 			//	Legend at the top of the chart, legend entries in a vertical column.
		'r', 				//	[Default] Legend to the right of the chart, legend entries in a vertical column.
		'l'					//	Legend to the left of the chart, legend entries in a vertical column.
	);
	
	/**
	 * An array of allowed legend order codes
	 *
	 * @var array
	 */
	public static $legend_orders = array(
		'l', 				//	[Default for vertical legends] Display labels in the order given to chdl.
		'r', 				//	Display labels in the reverse order as given to chdl. This is useful in stacked bar charts to show the legend in the same order as the bars appear.
		'a'					//	[Default for horizontal legends] Automatic ordering: roughly means sorting by length, shortest first, as measured in 10 pixel blocks. When two elements are the same length (divided into 10 pixel blocks), the one listed first will appear first.
	);
	
	/**
	 * An array of allowed visible axis values
	 *
	 * @var array
	 */
	public static $visible_axes = array(
		'x', 				//	Bottom x-axis
		't', 				//	Top x-axis [Not supported by Google-o-Meter]
		'y', 				//	Left y-axis
		'r'					//	Right y-axis [Not supported by Google-o-Meter]
	);
	
	/**
	 * An array of allowed text and data marker values
	 *
	 * @link http://code.google.com/apis/chart/image/docs/chart_params.html#gcharts_data_point_labels
	 * @var array
	 */
	public static $marker_types = array(
		'f', 				//	a flag marker containing text (eg "fMonth Total")
		't', 				//	just plain text (eg "tMonth Total")
		'A', 				//	
		'N' 				//	the value of the point formatted like this: <preceding_text>*<number_type><decimal_places>zs<x or y>*<following_text>
	);
	
	/**
	 * The current chart type.  Must be one of the values in Chart::$types
	 *
	 * @var string
	 */
	protected $type;
	
	/**
	 * The current chart width in px.  Set using Chart::setSize().
	 *
	 * @var int
	 */
	protected $width;
	
	/**
	 * Stores the current chart height in px.  Set using Chart::setSize().
	 *
	 * @var int
	 */
	protected $height;
	
	/**
	 * When set to true some chart types will generate a random colour
	 *
	 * @todo explain/document this better?
	 * @var bool
	 */
	protected $generateColor = false;
	
	/**
	 * The title of the current chart
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 * The colour of the title text in RRGGBB format
	 *
	 * @var string
	 */
	protected $titleColor;
	
	/**
	 * The title's font size in points
	 *
	 * @var int
	 */
	protected $titleSize;
	
	/**
	 * Whether to show a title on the chart or not
	 *
	 * @var bool
	 */
	protected $showTitle = true;
	
	/**
	 * The position of the legend
	 *
	 * @var string
	 */
	protected $legendPosition;
	
	/**
	 * The order of the legend items
	 *
	 * @var string
	 */
	protected $legendOrder;
	
	/**
	 * The colour of the legend text in RRGGBB format
	 *
	 * @var string
	 */
	protected $legendColor;
	
	/**
	 * The legend text size in points
	 *
	 * @var int
	 */
	protected $legendFontSize;
	
	/**
	 * Whether to show the legend or not
	 *
	 * @var bool
	 */
	protected $showLegend = true;
	
	
	/**
	 * The chart's left margin in px
	 *
	 * @var int
	 */
	protected $marginLeft;
	
	/**
	 * The chart's right margin in px
	 *
	 * @var int
	 */
	protected $marginRight;
	
	/**
	 * The chart's top margin in px
	 *
	 * @var int
	 */
	protected $marginTop;
	
	/**
	 * The chart's bottom margin in px
	 *
	 * @var int
	 */
	protected $marginBottom;
	
	/**
	 * The legend's width in px
	 *
	 * @var int
	 */
	protected $legendWidth;
	
	/**
	 * The legend's height in px
	 *
	 * @var int
	 */
	protected $legendHeight;
	
	/**
	 * The list of visible axes
	 *
	 * @var array
	 */
	protected $visibleAxes;
	
	/**
	 * The range of values for each axis
	 *
	 * @var array
	 */
	protected $axisRange;
	
	/**
	 * The labels for each axis
	 *
	 * @var array
	 */
	protected $axisLabels;
	
	/**
	 * The chart's background colour in RRGGBB format
	 *
	 * @var string
	 */
	protected $backgroundColor;
	
	/**
	 * The chart's background colour in RRGGBB format
	 *
	 * @var string
	 */
	protected $chartColor;
	
	
	/**
	 * The chart's transparency as a number between 0 and 255
	 *
	 * @var int
	 */
	protected $transparency;
	
	
	/**
	 * The type of marker to use
	 *
	 * @var string
	 */
	protected $markerType;
	
	/**
	 * The colour of the marker in RRGGBB format
	 *
	 * @var string
	 */
	protected $markerColor;
	
	/**
	 * The size of the marker in pixels
	 *
	 * @var int
	 */
	protected $markerSize;
	
	
	/**
	 * The chart's unique ID
	 *
	 * @var int
	 */
	protected $id;
	
	
	
	/**
	 * A nested array or formatting rules, which will be passed to Google Charts
	 *
	 * @var array
	 */
	public $formatting = array();
	
	
	/**
	 * Generates and returns a random colour in RRGGBB format
	 *
	 * @return string a hexadecimal colour
	 */
	public static function get_hexa_color() {
		$hexa = dechex(rand(0, hexdec('FFFFFF')));
		while(strlen($hexa) < 6) {
			$hexa = "0$hexa";
		}
		return $hexa;
	}

	
	/**
	 * 
	 */
	public function __construct() {
		parent::__construct();
		$this->id = self::$cpt++;
	}
	
	/**
	 * Returns a URL to this chart image on Google Charts
	 * 
	 * @param array $params An optional set of default chart parameters (eg for setting params in subclasses).  Will be overwritten by properties set on this chart)
	 * @return string
	 */
	public function Link(array $params = null) {
		if(!$params) {
			$params = array();
		}
		
		
		
		$params[self::$type_param] = $this->getTypeForLink();
		$params[self::$size_param] = "{$this->getFinalWidth()}x{$this->getFinalHeight()}";
		
		// Chart Title : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_chart_title
		
		if($this->title && $this->showTitle) {
			$title = str_replace(' ', '+', $this->title);
			$params[self::$title_param] = str_replace("\n", '|', $title);
			if($this->titleColor) {
				$params[self::$title_style_param] = $this->titleColor;
				if($this->titleSize) {
					$params[self::$title_style_param] .= ",$this->titleSize";
				}
			}
		}
		
		// Chart Margins : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_chart_margins
		
		if($this->marginTop) {
			$params[self::$margin_param] = "$this->marginLeft,$this->marginRight,$this->marginTop,$this->marginBottom";
		}
		
		// Chart Legend Text And Style : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_legend
		
		if(array_key_exists(self::$legend_labels_param, $params) && ($this->legendPosition || $this->legendOrder)) {
			if($this->legendPosition) {
				$params[self::$legend_position_param] = $this->legendPosition;
			}
			if($this->legendOrder) {
				if(array_key_exists(self::$legend_position_param, $params)) {
					$params[self::$legend_position_param] .= '|';
				}
				$params[self::$legend_position_param] .= (is_array($this->legendOrder) ? implode(',', $this->legendOrder) : $this->legendOrder);
			}
			if(array_key_exists(self::$margin_param, $params) && $this->legendWidth) {
				$params[self::$margin_param] .= "|$this->legendWidth,$this->legendHeight";
			}
			if($this->legendColor && $this->legendFontSize) {
				$params[self::$legend_style_param] = "$this->legendColor,$this->legendFontSize";
			}
		}
		
		
		
		// Axis Styles & Labels : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_axis_styles_labels
		
		if($this->visibleAxes) {
		
			$params[self::$visible_axes_param] = implode(',', $this->visibleAxes);
			if($this->axisRange) {
				
				foreach($this->axisRange as $index => $range) {
					if($range) {
						$ranges[] = "$index," . implode(',', $range);
					}
				}
				if(isset($ranges)) {
					$params[self::$axis_range_param] = implode('|', $ranges);
				}
			}
			if($this->axisLabels) {
				foreach($this->axisLabels as $index => $labels) {
					if($labels) {
						$axesLabels[] = "$index:|" . implode('|', $labels) . '|';
					}
				}
				if(isset($axesLabels)) {
					$params[self::$axis_labels_param] = implode('', $axesLabels);
				}
			}
		}
		
		// Background Fill : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_background_fills
		
		if($this->backgroundColor) {
			$backgroundFills[] = "bg,s,$this->backgroundColor";
		}
		if($this->chartColor) {
			$backgroundFills[] = "c,s,$this->chartColor";
		}
		if(!is_null($this->transparency)) {
			$backgroundFills[] = "a,s,000000$this->transparency";
		}
		if(isset($backgroundFills)) {
			$params[self::$background_fill_param] = implode('|', $backgroundFills);
		}
		
	//	$params['chds'] = 'a';
		
		foreach($params as $name => $value) {
			$paramValues[] = "$name=$value";
		}
		
		return self::$base_url . implode('&', $paramValues);
	}
	
	/**
	 * Standard SS method
	 * 
	 * @return string An <img> tag pointing to {@link Link()}
	 */
	public function forTemplate() {
		$link = Convert::raw2xml($this->Link());
		return "<img src=\"$link\"/>";
	}
	
	/**
	 * Returns the chart's type for use in {@link Link()} 
	 * 
	 * @return string The chart type
	 */
	public function getTypeForLink() {
		return $this->type;
	}
	
	/**
	 * Sets the chart's type
	 * 
	 * @param string The chart type (must be in this chart's list of valid types)
	 */
	public function setType($type) {
		$types = $this->stat('types');
		if(in_array($type, $types)) {
			$this->type = $type;
		}
	}
	
	/**
	 * Sets the chart's dimensions in pixels
	 * 
	 * @param int $width
	 * @param int $height (optional)
	 */
	public function setSize($width, $height = null) {
		$this->width = $width;
		$this->height = ($height ? $height : $width);
	}
	
	/**
	 * Gets the chart's width, falling back to {@link $default_width} if not set
	 * 
	 * @return int The width in pixels
	 */
	public function getFinalWidth() {
		return ($this->width ? $this->width : self::$default_width);
	}
	
	/**
	 * Gets the chart's height, falling back to {@link $default_height} if not set
	 * 
	 * @return int The height in pixels
	 */
	public function getFinalHeight() {
		return ($this->height ? $this->height : self::$default_height);
	}
	
	/**
	 * Set whether to generate a random colour for the chart ({@see $generateColor})
	 * 
	 * @param bool
	 */
	public function generateColor($value) {
		$this->generateColor = $value;
	}
	
	
	/**
	 * Set the chart's title text and style
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_chart_title
	 * @param string $title
	 * @param string $colour (optional) the title's colour in RRGGBB format
	 * @param string $size (optional) the title's font size in points
	 */
	public function setTitle($title, $color = null, $size = null) {
		$this->title = $title;
		if ($color) {
			$this->titleColor = $color;
		}
		if ($size) {
			$this->titleSize = $size;
		}
	}
	
	/**
	 * Set whether to to show the title on the chart
	 * 
	 * @param bool
	 */
	public function setShowTitle($show) {
		$this->showTitle = $show;
	}
	
	/**
	 * Set the chart's margins, in normal CSS clockwise order
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_chart_margins
	 * @param int $top The top margin in px
	 * @param int $right The right margin in px
	 * @param int $bottom The bottom margin in px
	 * @param int $left The left margin in px
	 */
	public function setMargins($top, $right, $bottom, $left) {
		$this->marginTop = $top;
		$this->marginRight = $right;
		$this->marginBottom = $bottom;
		$this->marginLeft = $left;
	}
	
	
	/**
	 * Sets the position of the legend
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_legend
	 * @see $legend_positions
	 * @param string The position (must be in the list of valid legend positions
	 */
	public function setLegendPosition($position) {
		if(in_array($position, $this->stat('legend_positions'))) {
			$this->legendPosition = $position;
		}
	}
	
	/**
	 * Sets the legend order
	 * 
	 * @see $legend_orders
	 * @param array|string Must be in the list of valid legend orders
	 */
	public function setLegendOrder($order) {
		if(is_array($order) || in_array($order, self::$legend_orders)) {
			$this->legendOrder = $order;
		}
	}
	
	/**
	 * Set the dimensions of the legend area
	 * 
	 * @param int $width width in pixels
	 * @param int $height height in pixels
	 */
	public function setLegendSize($width, $height) {
		$this->legendWidth = $width;
		$this->legendHeight = $height;
	}
	
	/**
	 * Sets the legend font size
	 * 
	 * @param int Font size in points
	 */
	public function setLegendFontSize($size) {
		$this->legendFontSize = $size;
	}
	
	/**
	 * Sets the colour of the legend text
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_legend
	 * @param string The colour in RRGGBB format
	 */
	public function setLegendColor($color) {
		$this->legendColor = $color;
	}
	
	/**
	 * Sets whether or not to show a legend
	 * 
	 * @todo this doesn't seem to be well supported, not sure if it should be moved to subclasses that use it or properly implemented in Chart
	 * @param bool
	 */
	public function setShowLegend($show) {
		$this->showLegend = $show;
	}
	
	
	
	

	/**
	 * Sets which axes show up on the chart
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#axis_type
	 * @see $visible_axes
	 * @param array An array of values that must be in the valid axis list
	 */
	public function setVisibleAxes($axes) {
		if (is_array($axes) && count(array_diff($axes, self::$visible_axes)) == 0) {
			$this->visibleAxes = $axes;
		}
	}
	
	
	/**
	 * Sets the range of values for each axis.
	 * 
	 * $ranges should be a nested array in the format 
	 *		array(
	 *			"axis index" => array(
	 *				"min value", 
	 *				"max value", 
	 *				(optional) "step size"
	 *			)
	 *		);
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#axis_range
	 * @todo make sure $ranges format is documented properly 
	 * @param array $ranges
	 */
	public function setAxesRange($ranges) {
		if(is_array($ranges)) {
			foreach($ranges as $index => $range) {
				if($range && is_array($range) && count($range) >= 2) {
					$this->axisRange[$index] = $range;
				}
			}
		}
	}
	

	/**
	 * Sets the range of values for each axis.
	 * 
	 * $axesLabels should be a nested array in the format 
	 *		array(
	 *			"axis index" => array(
	 *				"label 1", "label 2", "label 3"
	 *			)
	 *		);
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#axis_labels
	 * @param array $axeslabels
	 */
	public function setAxesLabels($axeslabels) {
		if(is_array($axeslabels)) {
			foreach($axeslabels as $index => $labels) {
				if($labels && is_array($labels)) {
					$this->axisLabels[$index] = $labels;
				}
			}
		}
	}
	
	/**
	 * In static/image charts, sets the background colour for the entire chart including axes, margins and legend.
	 * Can be used in interactive charts to set an array of properties (stroke, strokeWidth and fill) but recommend using setChartBorderWidth(), setChartBorderColor(), setChartColor() to set properties instead
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_solid_fills
	 * @param string|array $backgroundColour The background colour in RRGGBB format or any array of background properties
	 */
	public function setBackgroundColor($backgroundColor) {
		$this->backgroundColor = $backgroundColor;
	}
	
	/**
	 * Set the background colour for the chart itself (ie just the area inside the axes)
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_solid_fills
	 * @param string $chartColor The background colour in RRGGBB format
	 */
	public function setChartColor($chartColor) {
		$this->chartColor = $chartColor;
	}
	
	/**
	 * Make the whole chart transparent.  Input ranges from 1 (basically transparent) to 255 (solid).  
	 * Note that 0 will make the chart's background disappear but not fade the chart itself
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_solid_fills
	 * @param int $transparency A number between 0 and 255
	 */
	public function setTransparency($transparency) {
		$this->transparency = dechex(min(max(0, $transparency), 255));
	}
	
	
	/**
	 * Define markers that are applied to chart lines, bars etc.
	 * This only works for static charts and only certain chart types.  See the link below for more info
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/chart_params.html#gcharts_data_point_labels
	 * @param string $type This is a specifically formatted string starting with one of the keys defined in {@see $marker_types}
	 * @param string $colour The colour of the marker text, in RRGGBB format
	 * @param int $size The size of the marker in pixels
	 */
	public function setMarker($type, $color, $size) {
		if(in_array($type[0], self::$marker_types)) {
			$this->markerType = $type;
			$this->markerColor = $color;
			$this->markerSize = $size;
		}
	}
	
	/**
	 * Set rules for using Google Charts formatters.  
	 * $formats is a nested array.  To format numbers in column 1 as percentages, for example, do something like this:
	 *		array(
	 *			"number" => array(
	 *				1 => array(
	 *					"suffix" => '%',
	 *					'fractionDigits' => 0
	 *				)
	 *			)
	 *		);
	 * 
	 * @link http://code.google.com/apis/chart/interactive/docs/reference.html#formatters
	 * @todo This is currently only implemented for maps.  Needs to be extended to the JS files and methods of all chart types
	 * @todo This currently only supports number formatting.  Other formats need to be
	 * @param array $formats The array of arrays.  Will overwrite all existing formatting
	 */
	public function setFormatting(array $formats) {
		$this->formatting = $formats;
	}
	
	public function addFormatting(string $type, int $column, array $formatData) {
		if (!isset($this->formatting[$type])) {
			$this->formatting[$type] = array();
		}
		$this->formatting[$type][$column] = $formatData;
	}
	
}

