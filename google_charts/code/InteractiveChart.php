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
	 * A list of valid axis title positions
	 *
	 * @var array
	 */
	public static $axis_titles_positions = array('in', 'out', 'none');
	
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
	protected $gridlineColor;
	
	/**
	 * Updates array of parameters
	 */
	public function updateJavascriptParams(array &$options) {
		
		// Titles Position
		if($this->axisTitlesPosition) {
			$options['axisTitlesPosition'] = $this->axisTitlesPosition;
		}
		
		// Gridline Color
		if($this->gridlineColor) {
			$options['gridlineColor'] = $this->gridlineColor;
		}
		
	}
	
	/**
	 * Set the position of axis titles.
	 *
	 * @param string $positon Must be a value from {@see $axis_titles_positions}
	 */
	public function setAxisTitlesPosition($position) {
		if(in_array($position, self::$axis_titles_positions)) {
			$this->axisTitlesPosition = $position;
		}
	}
	
	/**
	 * Set the colour of the grid lines (eg the horizontal lines behind a bar or line graph)
	 *
	 * @param string $colour The colour in #RRGGBB format
	 */
	function setGridlineColor($color) {
		$this->gridlineColor = $color;
	}
	
}
