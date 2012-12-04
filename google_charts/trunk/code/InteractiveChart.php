<?php
/**
 * This class is used to add the interactive charts common functions and options
 *
 * @link http://code.google.com/apis/chart/interactive/docs/gallery.html
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 * @todo Inherit the legend_positions array values by putting it in this file
 */
class InteractiveChart extends Extension {
	/**
	 * Valid values for the chart's title position
	 *
	 * @var array
	 */
	public static $title_positions = array('in', 'out', 'none');
	
	/**
	 * The colour of the border in #RRGGBB format
	 *
	 * @var string
	 */
	protected $chartBorderColor;
	
	/**
	 * The width of the border in pixels
	 *
	 * @var int
	 */
	protected $chartBorderWidth;
	
	/**
	 * The width of the chart inside the border area
	 *
	 * @var int|string
	 */
	protected $chartWidth;
	
	/**
	 * The height of the chart inside the border area
	 *
	 * @var int|string
	 */
	protected $chartHeight;
	
	/**
	 * The chart's default font size in pixels
	 *
	 * @var int
	 */
	protected $fontSize;
	
	/**
	 * The chart's default font face
	 *
	 * @var string
	 */
	protected $fontName;
	
	/**
	 * The legend's font face
	 *
	 * @var string
	 */
	protected $legendFontName;
	
	/**
	 * Whether the data/categories should be reversed
	 *
	 * @var bool
	 */
	protected $reverseCategories;
	
	
	/**
	 * The title's font face
	 *
	 * @var string
	 */
	protected $titleFontName;
	
	/**
	 * The position of the chart's title (One of the positions in {@see InteractiveChart::$title_positions})
	 * Not usable with pie chart
	 * 
	 * @var string 
	 */
	protected $titlePosition;
	
	/**
	 * The colour of tooltip text in #RRGGBB format
	 *
	 * @var string
	 */
	protected $tooltipColor;
	
	/**
	 * The size of tooltip text in pixels
	 *
	 * @var int
	 */
	protected $tooltipFontSize;
	
	/**
	 * The name of the font to use for tooltip text
	 *
	 * @var string
	 */
	protected $tooltipFontName;
	
	/**
	 * A nested array or formatting rules, which will be passed to Google Charts
	 *
	 * @var array
	 */
	public $formatting = array();
	
	/**
	 * Updates array of parameters that will be passed to the JS chart.draw() function with InteractiveChart-specific settings
	 */
	public function updateJavascriptParams(array &$options) {
		
		// Dimensions
		$options['width'] = $this->owner->getFinalWidth();
		$options['height'] = $this->owner->getFinalHeight();
		
		
		$options['backgroundColor'] = array();
		
		// Background
		if($this->owner->backgroundColor) {
			$options['backgroundColor'] = $this->owner->backgroundColor;
		}
		if($this->chartBorderColor) {
		//	$options['backgroundColor.stroke'] = $this->chartBorderColor;
			$options['backgroundColor']['stroke'] = $this->chartBorderColor;
		}
		if($this->chartBorderWidth) {
		//	$options['backgroundColor.strokeWidth'] = $this->chartBorderWidth;
			$options['backgroundColor']['strokeWidth'] = $this->chartBorderWidth;
		}
		if($this->owner->chartColor) {
		//	$options['backgroundColor.fill'] = $this->owner->chartColor;
			$options['backgroundColor']['fill'] = $this->owner->chartColor;
		}
		
		if (count($options['backgroundColor']) == 0) {
			unset($options['backgroundColor']);
		}
		
		// Chart Area
		if(isset($this->owner->marginLeft)) {
			$chartAreaOptions['left'] = $this->owner->marginLeft;
		}
		if(isset($this->owner->marginTop)) {
			$chartAreaOptions['top'] = $this->owner->marginTop;
		}
		if($this->chartWidth) {
			$chartAreaOptions['width'] = $this->chartWidth;
		}
		if($this->chartHeight) {
			$chartAreaOptions['height'] = $this->chartHeight;
		}
		if(isset($chartAreaOptions)) {
			$options['chartArea'] = $chartAreaOptions;
		}
		
		// Font
		if($this->fontSize) {
			$options['fontSize'] = $this->fontSize;
		}
		if($this->fontName) {
			$options['fontName'] = $this->fontName;
		}
		
		// Legend
		if($this->owner->legendPosition) {
			$options['legend'] = $this->owner->legendPosition;
		}
		if($this->owner->legendColor) {
			$legendTextStyleOptions['color'] = $this->owner->legendColor;
		}
		if($this->owner->legendFontSize) {
			$legendTextStyleOptions['fontSize'] = $this->owner->legendFontSize;
		}
		if($this->legendFontName) {
			$legendTextStyleOptions['fontName'] = $this->legendFontName;
		}
		if(isset($legendTextStyleOptions)) {
			$options['legendTextStyle'] = $legendTextStyleOptions;
		}
		
		// Reverse Categories
		if($this->reverseCategories) {
			$options['reverseCategories'] = 'true';
		}
		
		// Title
		if($this->owner->showTitle) {
			if($this->owner->title) {
				$options['title'] = $this->owner->title;
			}
			if($this->titlePosition) {
				$options['titlePosition'] = $this->titlePosition;
			}
			if($this->owner->titleColor) {
				$titleTextStyleOptions['color'] = $this->owner->titleColor;
			}
			if($this->owner->titleSize) {
				$titleTextStyleOptions['fontSize'] = $this->owner->titleSize;
			}
			if($this->titleFontName) {
				$titleTextStyleOptions['fontName'] = $this->titleFontName;
			}
			if(isset($titleTextStyleOptions)) {
				$options['titleTextStyle'] = $titleTextStyleOptions;
			}
		}
		
		// Tooltip
		if($this->tooltipColor) {
			$tooltipTextStyleOptions['color'] = $this->tooltipColor;
		}
		if($this->tooltipFontSize) {
			$tooltipTextStyleOptions['fontSize'] = $this->tooltipFontSize;
		}
		if($this->tooltipFontName) {
			$tooltipTextStyleOptions['fontName'] = $this->tooltipFontName;
		}
		if(isset($tooltipTextStyleOptions)) {
			$options['tooltipTextStyle'] = $tooltipTextStyleOptions;
		}
		
		
		
		$formatting = $this->getFormatting();
		
		// label formatting
		if (count($formatting) > 0) {
			$options['formatting'] = $formatting;
		}
		
		

	}
	
	
	
	/**
	 * Set the chart's border colour (only works if border width is > 0)
	 *
	 * @param string $color The colour in #RRGGBB format
	 */
	public function setChartBorderColor($color) {
		$this->chartBorderColor = $color;
	}
	
	
	/**
	 * Set the chart's border width.  Defaults to 0 (no border)
	 *
	 * @param int $width The thickness of the border in pixels
	 */
	public function setChartBorderWidth($width) {
		$this->chartBorderWidth = $width;
	}
	
	/**
	 * Sets properties relating to the size/area of the chart itself inside the border box (which is set using {@see Chart::setSize()})
	 * Values can either be an int (in px) or another CSS compatible string (eg "20%").  All default to "auto".
	 *
	 * @param int|string $left The chart's distance from the left border
	 * @param int|string $top The chart's distance from the top border
	 * @param int|string $width The width of the actual chart
	 * @param int|string $height The height of the actual chart
	 */
	public function setChartArea($left, $top, $width, $height) {
		$this->owner->marginLeft = $left;
		$this->owner->marginTop = $top;
		$this->chartWidth = $width;
		$this->chartHeight = $height;
	}
	
	/**
	 * Set font properties for all text on the chart
	 *
	 * @param int $size The font size in pixels
	 * @param string $name The font name
	 */
	public function setFont($size, $name) {
		$this->fontSize = $size;
		$this->fontName = $name;
	}
	
	/**
	 * Set font used in the legend
	 *
	 * @param string $name The font name
	 */
	public function setLegendFontName($name) {
		$this->legendFontName = $name;
	}
	
	/**
	 * Reverses the order of categories (eg bar in a bar chart)
	 */
	public function reverseCategories() {
		$this->reverseCategories = true;
	}
	
	/**
	 * Set font used in the title
	 *
	 * @param string $name The font name
	 */
	public function setTitleFontName($name) {
		$this->titleFontName = $name;
	}
	
	
	/**
	 * Set the position of the chart's title
	 *
	 * @param string $position One of the positions in {@see InteractiveChart::$title_positions}
	 */
	public function setTitlePosition($position) {
		if(in_array($position, self::$title_positions)) {
			$this->titlePosition = $position;
		}
	}
	
	/**
	 * Set font properties for tooltip text
	 *
	 * @param string $color The text colour in #RRGGBB format
	 * @param string $fontName The font name
	 * @param int $fontSize The font size in pixels
	 */
	public function setTooltipTextStyle($color, $fontName, $fontSize) {
		$this->tooltipColor = $color;
		$this->tooltipFontName = $fontName;
		$this->tooltipFontSize = $fontSize;
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
	 * @todo This currently only supports number formatting.  Other formats need to be added
	 * @param array $formats The array of arrays.  Will overwrite all existing formatting
	 */
	public function setFormatting(array $formats) {
		$this->formatting = $formats;
	}
	
	/**
	 * Get rules for using Google Charts formatters.  
	 * 
	 * @return array
	 */
	public function getFormatting() {
		return $this->formatting;
	}
	
	
	/**
	 * Set rules for using Google Charts formatters.  
	 * This is like setFormatting() but only adds a single formatter to a single column, rather than setting/replacing all formatters.
	 * 
	 * @link http://code.google.com/apis/chart/interactive/docs/reference.html#formatters
	 * @param string $type The formatter type (eg "number")
	 * @param int $column The column to apply the formatting to
	 * @param array $formatData An array of properties to be passed to the formatter. See the link above for valid options
	 */
	public function addFormatting(string $type, int $column, array $formatData) {
		if (!isset($this->formatting[$type])) {
			$this->formatting[$type] = array();
		}
		
		$this->formatting[$type][$column] = $formatData;
	}
	
}

/**
 * This class is used to add the interactive charts axis functions and options for line, bar and column charts
 *
 * @link http://code.google.com/apis/chart/interactive/docs/gallery.html
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 * @todo Finish to add the hAxis and vAxis functions
 */
class InteractiveChart_Axis extends Extension {
	/**
	 * A list of valid positions
	 *
	 * @var array
	 */
	public static $positions = array('in', 'out', 'none');
	
	public static $view_window_modes = array('pretty', 'maximized', 'explicit');
	
	/**
	 * The current axis title position
	 *
	 * @var string
	 */
	protected $axisTitlesPosition;
	
	/**
	 * The colour of the grid lines in #RRGGBB format
	 *
	 * @var string
	 */
	
	protected $hAxisBaseline, $hAxisBaselineColor;
	protected $reverseHAxisDirection;
	protected $hAxisFormat;
	protected $hAxisGridlineColor;
	protected $hAxisLogScale;
	protected $hAxisTextPosition;
	protected $hAxisTextColor, $hAxisTextFontName, $hAxisTextFontSize;
	protected $hAxisTitle, $hAxisTitleColor, $hAxisTitleFontName, $hAxisTitleFontSize;
	protected $hAxisMin, $hAxisMax;
	protected $hAxisViewWindowMode, $hAxisViewWindowMin, $hAxisViewWindowMax;
	
	protected $hAxisSlantedText, $hAxisSlantedTextAngle;
	protected $hAxisMaxAlternation;
	protected $hAxisShowTextEvery;
	
	protected $vAxisBaseline, $vAxisBaselineColor;
	protected $reverseVAxisDirection;
	protected $vAxisFormat;
	protected $vAxisGridlineColor;
	protected $vAxisLogScale;
	protected $vAxisTextPosition;
	protected $vAxisTextColor, $vAxisTextFontName, $vAxisTextFontSize;
	protected $vAxisTitle, $vAxisTitleColor, $vAxisTitleFontName, $vAxisTitleFontSize;
	protected $vAxisMin, $vAxisMax;
	protected $vAxisViewWindowMode, $vAxisViewWindowMin, $vAxisViewWindowMax;
	
	/**
	 * Updates array of parameters
	 */
	public function updateJavascriptParams(array &$options) {
		
		// Titles Position
		if($this->axisTitlesPosition) {
			$options['axisTitlesPosition'] = $this->axisTitlesPosition;
		}
		
		// Horizontal Axis Baseline
		if($this->hAxisBaseline !== null) {
			$hAxis['baseline'] = $this->hAxisBaseline;
		}
		if($this->hAxisBaselineColor) {
			$hAxis['baselineColor'] = $this->hAxisBaselineColor;
		}
		
		// Horizontal Axis Direction
		if($this->reverseHAxisDirection) {
			$hAxis['direction'] = -1;
		}
		
		// Horizontal Axis Format
		if($this->hAxisFormat) {
			$hAxis['format'] = $this->hAxisFormat;
		}
		
		// Horizontal Axis Gridline Color
		if($this->hAxisGridlineColor) {
			$hAxis['gridlineColor'] = $this->hAxisGridlineColor;
		}
		
		// Horizontal Axis Log Scale
		if($this->hAxisLogScale) {
			$hAxis['logScale'] = true;
		}
		
		// Horizontal Axis Text Position
		if($this->hAxisTextPosition) {
			$hAxis['textPosition'] = $this->hAxisTextPosition;
		}
		
		// Horizontal Axis Text Style
		if($this->hAxisTextColor) {
			$hAxisTextStyle['color'] = $this->hAxisTextColor;
		}
		if($this->hAxisTextFontName) {
			$hAxisTextStyle['fontName'] = $this->hAxisTextFontName;
		}
		if($this->hAxisTextFontSize) {
			$hAxisTextStyle['fontSize'] = $this->hAxisTextFontSize;
		}
		if(isset($hAxisTextStyle)) {
			$hAxis['textStyle'] = $hAxisTextStyle;
		}
		
		// Horizontal Axis Title
		if($this->hAxisTitle) {
			$hAxis['title'] = $this->hAxisTitle;
		}
		
		// Horizontal Axis Title Style
		if($this->hAxisTitleColor) {
			$hAxisTitleStyle['color'] = $this->hAxisTitleColor;
		}
		if($this->hAxisTitleFontName) {
			$hAxisTitleStyle['fontName'] = $this->hAxisTitleFontName;
		}
		if($this->hAxisTitleFontSize) {
			$hAxisTitleStyle['fontSize'] = $this->hAxisTitleFontSize;
		}
		if(isset($hAxisTitleStyle)) {
			$hAxis['textStyle'] = $hAxisTitleStyle;
		}
		
		// Horizontal Axis Min & Max
		if($this->hAxisMin !== null) {
			$hAxis['minValue'] = $this->hAxisMin;
		}
		if($this->hAxisMax !== null) {
			$hAxis['maxValue'] = $this->hAxisMax;
		}
		
		// Horizontal Axis View Window
		if($this->hAxisViewWindowMode) {
			$hAxis['viewWindowMode'] = $this->hAxisViewWindowMode;
		}
		if($this->hAxisViewWindowMin !== null) {
			$hAxisViewWindow['min'] = $this->hAxisViewWindowMin;
		}
		if($this->hAxisViewWindowMax !== null) {
			$hAxisViewWindow['max'] = $this->hAxisViewWindowMax;
		}
		if(isset($hAxisViewWindow)) {
			$hAxis['viewWindow'] = $hAxisViewWindow;
		}
		
		// Horizontal Axis Slanted Text
		if($this->hAxisSlantedText !== null) {
			$hAxis['slantedText'] = $this->hAxisSlantedText ? true : false;
		}
		if($this->hAxisSlantedTextAngle) {
			$hAxis['slantedTextAngle'] = $this->hAxisSlantedTextAngle;
		}
		
		// Horizontal Axis Max Alteration
		if($this->hAxisMaxAlternation) {
			$hAxis['maxAlternation'] = $this->hAxisMaxAlternation;
		}
		
		// Horizontal Axis Show Text Every
		if($this->hAxisShowTextEvery) {
			$hAxis['showTextEvery'] = $this->hAxisShowTextEvery;
		}
		
		if(isset($hAxis)) {
			$options['hAxis'] = $hAxis;
		}
		
		// Vertical Axis Baseline
		if($this->vAxisBaseline !== null) {
			$vAxis['baseline'] = $this->vAxisBaseline;
		}
		if($this->vAxisBaselineColor) {
			$vAxis['baselineColor'] = $this->vAxisBaselineColor;
		}
		
		// Vertical Axis Direction
		if($this->reverseVAxisDirection) {
			$vAxis['direction'] = -1;
		}
		
		// Vertical Axis Format
		if($this->vAxisFormat) {
			$vAxis['format'] = $this->vAxisFormat;
		}
		
		// Vertical Axis Gridline Color
		if($this->vAxisGridlineColor) {
			$vAxis['gridlineColor'] = $this->vAxisGridlineColor;
		}
		
		// Vertical Axis Log Scale
		if($this->vAxisLogScale) {
			$vAxis['logScale'] = true;
		}
		
		// Vertical Axis Text Position
		if($this->vAxisTextPosition) {
			$vAxis['textPosition'] = $this->vAxisTextPosition;
		}
		
		// Vertical Axis Text Style
		if($this->vAxisTextColor) {
			$vAxisTextStyle['color'] = $this->vAxisTextColor;
		}
		if($this->vAxisTextFontName) {
			$vAxisTextStyle['fontName'] = $this->vAxisTextFontName;
		}
		if($this->vAxisTextFontSize) {
			$vAxisTextStyle['fontSize'] = $this->vAxisTextFontSize;
		}
		if(isset($vAxisTextStyle)) {
			$vAxis['textStyle'] = $vAxisTextStyle;
		}
		
		// Vertical Axis Title
		if($this->vAxisTitle) {
			$vAxis['title'] = $this->vAxisTitle;
		}
		
		// Vertical Axis Title Style
		if($this->vAxisTitleColor) {
			$vAxisTitleStyle['color'] = $this->vAxisTitleColor;
		}
		if($this->vAxisTitleFontName) {
			$vAxisTitleStyle['fontName'] = $this->vAxisTitleFontName;
		}
		if($this->vAxisTitleFontSize) {
			$vAxisTitleStyle['fontSize'] = $this->vAxisTitleFontSize;
		}
		if(isset($vAxisTitleStyle)) {
			$vAxis['textStyle'] = $vAxisTitleStyle;
		}
		
		// Vertical Axis Min & Max
		if($this->vAxisMin !== null) {
			$vAxis['minValue'] = $this->vAxisMin;
		}
		if($this->vAxisMax !== null) {
			$vAxis['maxValue'] = $this->vAxisMax;
		}
		
		// Vertical Axis View Window
		if($this->vAxisViewWindowMode) {
			$vAxis['viewWindowMode'] = $this->vAxisViewWindowMode;
		}
		if($this->vAxisViewWindowMin !== null) {
			$vAxisViewWindow['min'] = $this->vAxisViewWindowMin;
		}
		if($this->vAxisViewWindowMax !== null) {
			$vAxisViewWindow['max'] = $this->vAxisViewWindowMax;
		}
		if(isset($vAxisViewWindow)) {
			$vAxis['viewWindow'] = $vAxisViewWindow;
		}
		
		if(isset($vAxis)) {
			$options['vAxis'] = $vAxis;
		}
	}
	
	/**
	 * Set the position of axis titles.
	 *
	 * @param string $positon Must be a value from {@see $axis_titles_positions}
	 */
	public function setAxisTitlesPosition($position) {
		if(in_array($position, self::$positions)) {
			$this->axisTitlesPosition = $position;
		}
	}
	
	/**
	 * Set the colour of the grid lines (eg the horizontal lines behind a bar or line graph)
	 *
	 * @param string $colour The colour in #RRGGBB format
	 */
	
	
	// Horizontal Axis
	
	public function setHAxisBaseline($baseline) {
		$this->hAxisBaseline = $baseline;
	}
	
	public function setHAxisBaselineColor($color) {
		$this->hAxisBaselineColor = $color;
	}
	
	public function reverseHAxisDirection() {
		$this->reverseHAxisDirection = true;
	}
	
	public function setHAxisFormat($format) {
		$this->hAxisFormat = $format;
	}
	
	public function setHAxisGridlineColor($color) {
		$this->hAxisGridlineColor = $color;
	}
	
	public function setHAxisLogScale() {
		$this->hAxisLogScale = true;
	}
	
	public function setHAxisTextPosition($position) {
		if(in_array($position, self::$positions)) {
			$this->hAxisTextPosition = $position;
		}
	}
	
	public function setHAxisTextStyle($color, $fontName, $fontSize) {
		$this->hAxisTextColor = $color;
		$this->hAxisTextFontName = $fontName;
		$this->hAxisTextFontSize = $fontSize;
	}
	
	public function setHAxisTitle($title) {
		$this->hAxisTitle = $title;
	}
	
	public function setHAxisTitleTextStyle($color, $fontName, $fontSize) {
		$this->hAxisTitleColor = $color;
		$this->hAxisTitleFontName = $fontName;
		$this->hAxisTitleFontSize = $fontSize;
	}
	
	public function setHAxisMin($value) {
		$this->hAxisMin = $value;
	}
	
	public function setHAxisMax($value) {
		$this->hAxisMax = $value;
	}
	
	public function setHAxisViewWindowMode($mode) {
		if(in_array($mode, self::$view_window_modes)) {
			$this->hAxisViewWindowMode = $mode;
		}
	}
	
	public function setHAxisViewWindowMin($value) {
		$this->hAxisViewWindowMin = $value;
	}
	
	public function setHAxisViewWindowMax($value) {
		$this->hAxisViewWindowMax = $value;
	}
	
	public function setHAxisSlantedText($slanted, $angle = null) {
		$this->hAxisSlantedText = $slanded;
		$this->hAxisSlantedTextAngle = $angle;
	}
	
	public function setHAxisMaxAlternation($alternation) {
		$this->hAxisMaxAlternation = $alternation;
	}
	
	public function setHAxisShowTextEvery($number) {
		$this->hAxisShowTextEvery = $number;
	}
	
	// Vertical Axis
	
	public function setVAxisBaseline($baseline) {
		$this->vAxisBaseline = $baseline;
	}
	
	public function setVAxisBaselineColor($color) {
		$this->vAxisBaselineColor = $color;
	}
	
	public function reverseVAxisDirection() {
		$this->reverseVAxisDirection = true;
	}
	
	public function setVAxisFormat($format) {
		$this->vAxisFormat = $format;
	}
	
	public function setVAxisGridlineColor($color) {
		$this->vAxisGridlineColor = $color;
	}
	
	public function setVAxisLogScale() {
		$this->vAxisLogScale = true;
	}
	
	public function setVAxisTextPosition($position) {
		if(in_array($position, self::$positions)) {
			$this->vAxisTextPosition = $position;
		}
	}
	
	public function setVAxisTextStyle($color, $fontName, $fontSize) {
		$this->vAxisTextColor = $color;
		$this->vAxisTextFontName = $fontName;
		$this->vAxisTextFontSize = $fontSize;
	}
	
	public function setVAxisTitle($title) {
		$this->vAxisTitle = $title;
	}
	
	public function setVAxisTitleTextStyle($color, $fontName, $fontSize) {
		$this->vAxisTitleColor = $color;
		$this->vAxisTitleFontName = $fontName;
		$this->vAxisTitleFontSize = $fontSize;
	}
	
	public function setVAxisMin($value) {
		$this->vAxisMin = $value;
	}
	
	public function setVAxisMax($value) {
		$this->vAxisMax = $value;
	}
	
	public function setVAxisViewWindowMode($mode) {
		if(in_array($mode, self::$view_window_modes)) {
			$this->vAxisViewWindowMode = $mode;
		}
	}
	
	public function setVAxisViewWindowMin($value) {
		$this->vAxisViewWindowMin = $value;
	}
	
	public function setVAxisViewWindowMax($value) {
		$this->vAxisViewWindowMax = $value;
	}
}
