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
	 * 
	 *
	 * @var 
	 */
	protected $chartBorderColor;
	
	protected $chartBorderWidth;
	protected $chartWidth, $chartHeight;
	protected $fontSize, $fontName;
	protected $legendFontName;
	protected $reverseCategories;
	protected $titleFontName, $titlePosition; // Position not usable for pie charts
	protected $tooltipColor, $tooltipFontSize, $tooltipFontName;
	
	
	
	public function updateJavascriptParams(array &$options) {
		
		// Dimensions
		
		$options['width'] = $this->owner->getFinalWidth();
		$options['height'] = $this->owner->getFinalHeight();
		
		// Background
		
		if($this->owner->backgroundColor) $options['backgroundColor'] = $this->owner->backgroundColor;
		if($this->chartBorderColor) $options['backgroundColor.stroke'] = $this->chartBorderColor;
		if($this->chartBorderWidth) $options['backgroundColor.strokeWidth'] = $this->chartBorderWidth;
		if($this->owner->chartColor) $options['backgroundColor.fill'] = $this->owner->chartColor;
		
		// Chart Area
		
		if(isset($this->owner->marginLeft)) $chartAreaOptions['left'] = $this->owner->marginLeft;
		if(isset($this->owner->marginTop)) $chartAreaOptions['top'] = $this->owner->marginTop;
		if($this->chartWidth) $chartAreaOptions['width'] = $this->chartWidth;
		if($this->chartHeight) $chartAreaOptions['height'] = $this->chartHeight;
		if(isset($chartAreaOptions)) {
			$options['chartArea'] = $chartAreaOptions;
		}
		
		// Font
		
		if($this->fontSize) $options['fontSize'] = $this->fontSize;
		if($this->fontName) $options['fontName'] = $this->fontName;
		
		// Legend
		
		if($this->owner->legendPosition) $options['legend'] = $this->owner->legendPosition;
		if($this->owner->legendColor) $legendTextStyleOptions['color'] = $this->owner->legendColor;
		if($this->owner->legendFontSize) $legendTextStyleOptions['fontSize'] = $this->owner->legendFontSize;
		if($this->legendFontName) $legendTextStyleOptions['fontName'] = $this->legendFontName;
		if(isset($legendTextStyleOptions)) {
			$options['legendTextStyle'] = $legendTextStyleOptions;
		}
		
		// Reverse Categories
		
		if($this->reverseCategories) $options['reverseCategories'] = 'true';
		
		// Title
		
		if($this->owner->showTitle) {
			if($this->owner->title) $options['title'] = $this->owner->title;
			if($this->titlePosition) $options['titlePosition'] = $this->titlePosition;
			if($this->owner->titleColor) $titleTextStyleOptions['color'] = $this->owner->titleColor;
			if($this->owner->titleSize) $titleTextStyleOptions['fontSize'] = $this->owner->titleSize;
			if($this->titleFontName) $titleTextStyleOptions['fontName'] = $this->titleFontName;
			if(isset($titleTextStyleOptions)) {
				$options['titleTextStyle'] = $titleTextStyleOptions;
			}
		}
		
		// Tooltip
		
		if($this->tooltipColor) $tooltipTextStyleOptions['color'] = $this->tooltipColor;
		if($this->tooltipFontSize) $tooltipTextStyleOptions['fontSize'] = $this->tooltipFontSize;
		if($this->tooltipFontName) $tooltipTextStyleOptions['fontName'] = $this->tooltipFontName;
		if(isset($tooltipTextStyleOptions)) {
			$options['tooltipTextStyle'] = $tooltipTextStyleOptions;
		}
	}
	
	// Background
	
	public function setChartBorderColor($color) {
		$this->chartBorderColor = $color;
	}
	
	public function setChartBorderWidth($width) {
		$this->chartBorderWidth = $width;
	}
	
	// Chart Area
	
	public function setChartArea($left, $top, $width, $height) {
		$this->owner->marginLeft = $left;
		$this->owner->marginTop = $top;
		$this->chartWidth = $width;
		$this->chartHeight = $height;
	}
	
	// Font
	
	public function setFont($size, $name) {
		$this->fontSize = $size;
		$this->fontName = $name;
	}
	
	// Legend
	
	public function setLegendFontName($name) {
		$this->legendFontName = $name;
	}
	
	// Reverse Categories
	
	public function reverseCategories() {
		$this->reverseCategories = true;
	}
	
	// Title
	
	public function setTitleFontName($name) {
		$this->titleFontName = $name;
	}
	
	public function setTitlePosition($position) {
		if(in_array($position, self::$title_positions)) {
			$this->titlePosition = $position;
		}
	}
	
	// Tooltip
	
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
	
	public static $axis_titles_positions = array('in', 'out', 'none');
	
	protected $axisTitlesPosition;
	protected $gridlineColor;
	
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
	
	// Titles Position
	
	public function setAxisTitlesPosition($position) {
		if(in_array($position, self::$axis_titles_positions)) {
			$this->axisTitlesPosition = $position;
		}
	}
	
	// Gridline Color
	
	function setGridlineColor($color) {
		$this->gridlineColor = $color;
	}
	
}
