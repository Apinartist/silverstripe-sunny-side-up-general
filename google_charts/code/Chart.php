<?php

/**
 * This class is used to create an image chart using google charts
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
	
	static $types = array();
	static $type_param = 'cht';
	static $data_param = 'chd';
	static $size_param = 'chs';
	static $color_param = 'chco';
	static $title_param = 'chtt';
	static $title_style_param = 'chts';
	static $data_scaling_param = 'chds';
	static $legend_labels_param = 'chdl';
	static $legend_settings_param = 'chdlp';
	static $margin_param = 'chma';
	static $visible_axes_param = 'chxt';
	static $axis_range_param = 'chxr';
	static $axis_labels_param = 'chxl';
	
	static $legend_positions = array('b', 'bv', 't', 'tv', 'r', 'l');
	static $legend_orders = array('l', 'r', 'a');
	static $visible_axes = array('x', 't', 'y', 'r');
	
	protected $type;
	protected $width, $height;
	protected $generateColor = false;
	protected $title, $titleColor, $titleSize;
	protected $legendPosition, $legendOrder;
	protected $marginLeft, $marginRight, $marginTop, $marginBottom, $legendWidth, $legendHeight;
	protected $visibleAxes, $axisRange, $axisLabels;
	
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
		$params[self::$type_param] = $this->type;
		$params[self::$size_param] = ($this->width ? $this->width : self::$default_width) . 'x' . ($this->height ? $this->height : self::$default_height);
		if($this->title) {
			$title = str_replace(' ', '+', $this->title);
			$params[self::$title_param] = str_replace("\n", '|', $title);
			if($this->titleColor) {
				$params[self::$title_style_param] = $this->titleColor;
				if($this->titleSize) $params[self::$title_style_param] .= ",$this->titleSize";
			}
		}
		if($this->marginTop) $params[self::$margin_param] = "$this->marginLeft,$this->marginRight,$this->marginTop,$this->marginBottom";
		if(array_key_exists(self::$legend_labels_param, $params) && ($this->legendPosition || $this->legendOrder)) {
			if($this->legendPosition) $params[self::$legend_settings_param] = $this->legendPosition;
			if($this->legendOrder) {
				if(array_key_exists(self::$legend_settings_param, $params)) $params[self::$legend_settings_param] .= '|';
				$params[self::$legend_settings_param] .= is_array($this->legendOrder) ? implode(',', $this->legendOrder) : $this->legendOrder;
			}
			if(array_key_exists(self::$margin_param, $params) && $this->legendWidth) $params[self::$margin_param] .= "|$this->legendWidth,$this->legendHeight";
		}
		if($this->visibleAxes) {
			$params[self::$visible_axes_param] = implode(',', $this->visibleAxes);
			if($this->axisRange) {
				foreach($this->axisRange as $index => $range) {
					if($range) $ranges[] = "$index," . implode(',', $range);
				}
				if(isset($ranges)) $params[self::$axis_range_param] = implode('|', $ranges);
			}
			if($this->axisLabels) {
				foreach($this->axisLabels as $index => $labels) {
					if($labels) $axesLabels[] = "$index:|" . implode('|', $labels);
				}
				if(isset($axesLabels)) $params[self::$axis_labels_param] = implode('', $axesLabels);
			}
		}
		foreach($params as $name => $value) $paramValues[] = "$name=$value";
		return self::$base_url . implode('&', $paramValues);
	}
	
	function setType($type) {
		$types = $this->stat('types');
		if(in_array($type, $types)) $this->type = $type;
	}
	
	function setSize($width, $height = null) {
		$this->width = $width;
		$this->height = $height ? $height : $width;
	}
	
	function generateColor($value) {
		$this->generateColor = $value;
	}
	
	function setTitle($title, $color = null, $size = null) {
		$this->title = $title;
		if($color) $this->titleColor = $color;
		if($size) $this->titleSize = $size;
	}
	
	function setLegendPosition($position) {
		if(in_array($position, $this->stat('legend_positions'))) $this->legendPosition = $position;
	}
	
	function setLegendOrder($order) {
		if(is_array($order) || in_array($order, self::$legend_orders)) $this->legendOrder = $order;
	}
	
	function setMargins($top, $right, $bottom, $left) {
		$this->marginTop = $top;
		$this->marginRight = $right;
		$this->marginBottom = $bottom;
		$this->marginLeft = $left;
	}
	
	function setLegendSize($width, $height) {
		$this->legendWidth = $width;
		$this->legendHeight = $height;
	}
	
	function setVisibleAxes($axes) {
		if(is_array($axes) && count(array_diff($axes, self::$visible_axes)) == 0) $this->visibleAxes = $axes;
	}
	
	function setAxesRange($ranges) {
		if(is_array($ranges)) {
			foreach($ranges as $index => $range) {
				if($range && is_array($range) && count($range) >= 2) $this->axisRange[$index] = $range;
			}
		}
	}
	
	function setAxesLabels($axeslabels) {
		if(is_array($axeslabels)) {
			foreach($axeslabels as $index => $labels) {
				if($labels && is_array($labels)) $this->axisLabels[$index] = $labels;
			}
		}
	}
	
	static function get_hexa_color() {
		$hexa = dechex(rand(0, hexdec('FFFFFF')));
		while(strlen($hexa) < 6) $hexa = "0$hexa";
		return $hexa;
	}
}

?>