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
	
	static $base_url = 'http://chart.apis.google.com/chart?';
	
	static $default_width = 250;
	static $default_height = 100;
	
	static $pixels_max = 300000;
	
	static $types = array();
	static $type_param = 'cht';
	static $data_param = 'chd';
	static $size_param = 'chs';
	static $color_param = 'chco';
	static $title_param = 'chtt';
	static $title_style_param = 'chts';
	static $data_scaling_param = 'chds';
	static $legend_labels_param = 'chdl';
	static $legend_position_param = 'chdlp';
	static $legend_style_param = 'chdls';
	static $margin_param = 'chma';
	static $visible_axes_param = 'chxt';
	static $axis_range_param = 'chxr';
	static $axis_labels_param = 'chxl';
	static $background_fill_param = 'chf';
	static $marker_param = 'chm';
	
	static $legend_positions = array('b', 'bv', 't', 'tv', 'r', 'l');
	static $legend_orders = array('l', 'r', 'a');
	static $visible_axes = array('x', 't', 'y', 'r');
	static $marker_types = array('f', 't', 'A', 'N');
	
	protected $type;
	protected $width, $height;
	protected $generateColor = false;
	protected $title, $titleColor, $titleSize, $showTitle = true;
	protected $legendPosition, $legendOrder, $legendColor, $legendFontSize, $showLegend = true;
	protected $marginLeft, $marginRight, $marginTop, $marginBottom, $legendWidth, $legendHeight;
	protected $visibleAxes, $axisRange, $axisLabels;
	protected $backgroundColor, $chartColor, $transparency;
	protected $markerType, $markerColor, $markerSize;
	
	static $cpt = 0;
	protected $id;
	
	function __construct() {
		parent::__construct();
		$this->id = self::$cpt++;
	}
	
	function forTemplate() {
		$link = Convert::raw2xml($this->Link());
		return "<img src=\"$link\"/>";
	}
	
	function Link(array $params = null) {
		if(! $params) $params = array();
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
				if(array_key_exists(self::$legend_position_param, $params)) $params[self::$legend_position_param] .= '|';
				$params[self::$legend_position_param] .= is_array($this->legendOrder) ? implode(',', $this->legendOrder) : $this->legendOrder;
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
					if($range) $ranges[] = "$index," . implode(',', $range);
				}
				if(isset($ranges)) {
					$params[self::$axis_range_param] = implode('|', $ranges);
				}
			}
			if($this->axisLabels) {
				foreach($this->axisLabels as $index => $labels) {
					if($labels) $axesLabels[] = "$index:|" . implode('|', $labels);
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
		if($this->transparency) {
			$backgroundFills[] = "a,s,000000$this->transparency";
		}
		if(isset($backgroundFills)) {
			$params[self::$background_fill_param] = implode('|', $backgroundFills);
		}
		
		foreach($params as $name => $value) $paramValues[] = "$name=$value";
		return self::$base_url . implode('&', $paramValues);
	}
	
	function getTypeForLink() {
		return $this->type;
	}
	
	function setType($type) {
		$types = $this->stat('types');
		if(in_array($type, $types)) $this->type = $type;
	}
	
	function setSize($width, $height = null) {
		$this->width = $width;
		$this->height = $height ? $height : $width;
	}
	
	function getFinalWidth() {
		return $this->width ? $this->width : self::$default_width;
	}
	
	function getFinalHeight() {
		return $this->height ? $this->height : self::$default_height;
	}
	
	function generateColor($value) {
		$this->generateColor = $value;
	}
	
	static function get_hexa_color() {
		$hexa = dechex(rand(0, hexdec('FFFFFF')));
		while(strlen($hexa) < 6) $hexa = "0$hexa";
		return $hexa;
	}
	
	// Chart Title : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_chart_title
	
	function setTitle($title, $color = null, $size = null) {
		$this->title = $title;
		if($color) $this->titleColor = $color;
		if($size) $this->titleSize = $size;
	}
	
	function setShowTitle($show) {$this->showTitle = $show;}
	
	// Chart Margins : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_chart_margins
	
	function setMargins($top, $right, $bottom, $left) {
		$this->marginTop = $top;
		$this->marginRight = $right;
		$this->marginBottom = $bottom;
		$this->marginLeft = $left;
	}
	
	// Chart Legend Text And Style : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_legend
	
	function setLegendPosition($position) {
		if(in_array($position, $this->stat('legend_positions'))) $this->legendPosition = $position;
	}
	
	function setLegendOrder($order) {
		if(is_array($order) || in_array($order, self::$legend_orders)) $this->legendOrder = $order;
	}
	
	function setLegendSize($width, $height) {
		$this->legendWidth = $width;
		$this->legendHeight = $height;
	}
	
	function setLegendFontSize($size) {$this->legendFontSize = $size;}
	function setLegendColor($color) {$this->legendColor = $color;}
	
	function setShowLegend($show) {$this->showLegend = $show;}
	
	// Visible Axes : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#axis_type
	
	function setVisibleAxes($axes) {
		if(is_array($axes) && count(array_diff($axes, self::$visible_axes)) == 0) $this->visibleAxes = $axes;
	}
	
	// Axis Range : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#axis_range
	
	function setAxesRange($ranges) {
		if(is_array($ranges)) {
			foreach($ranges as $index => $range) {
				if($range && is_array($range) && count($range) >= 2) $this->axisRange[$index] = $range;
			}
		}
	}
	
	// Custom Axis Labels : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#axis_labels
	
	function setAxesLabels($axeslabels) {
		if(is_array($axeslabels)) {
			foreach($axeslabels as $index => $labels) {
				if($labels && is_array($labels)) $this->axisLabels[$index] = $labels;
			}
		}
	}
	
	// Solid Fills : http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#gcharts_solid_fills
	
	function setBackgroundColor($backgroundColor) {
		$this->backgroundColor = $backgroundColor;
	}
	
	function setChartColor($chartColor) {
		$this->chartColor = $chartColor;
	}
	
	function setTransparency($transparency) {
		$this->transparency = dechex(min(max(0, $transparency), 255));
	}
	
	function setMarker($type, $color, $size) {
		if(in_array($type[0], self::$marker_types)) {
			$this->markerType = $type;
			$this->markerColor = $color;
			$this->markerSize = $size;
		}
	}
}

?>