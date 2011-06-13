<?php

/**
 * This class is used to create a pie chart as an image using google charts
 *
 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class PieChart extends Chart {

	protected $type = 'p';

	static $types = array('p', 'p3', 'pc');

	protected $portions = array();

	static $labels_param = 'chl';
	static $rotation_param = 'chp';

	protected $colors = array();
	protected $rotation;
	protected $showLabels = true;

	function addPortion($portion, $color = null, $legend = null, $label = null) {
		$portion = array('value' => $portion);
		if($color) $portion['color'] = $color;
		if($legend) $portion['legend'] = $legend;
		if($label) $portion['label'] = $label;
		$this->portions[] = $portion;
	}

	function Link(array $params = null) {
		if(count($this->portions) > 0) {
			foreach($this->portions as $portion) {
				$portions[] = $portion['value'];
				if(isset($portion['color'])) $colors[] = str_replace('#', '', $portion['color']);
				else if($this->generateColor) $colors[] = Chart::get_hexa_color();
				if(isset($portion['legend'])) $legend[] = str_replace(' ', '+', $portion['legend']);
				if(isset($portion['label'])) $label[] = str_replace(' ', '+', $portion['label']);
			}
			$total = array_sum($portions);
			if($total == 0) {
				$total = 1;
			}
			foreach($portions as $i => $portion) $portions[$i] = round($portion * 100 / $total);
			$params[Chart::$data_param] = 't:' . implode(',', $portions);
			if(isset($colors)) $params[Chart::$color_param] = implode(count($colors) == count($this->portions) ? '|' : ',', $colors);
			else if(count($this->colors) > 0) $params[Chart::$color_param] = implode(',', $this->colors);
			if(isset($legend) && $this->showLegend) $params[Chart::$legend_labels_param] = implode('|', $legend);
			if(isset($label) && $this->showLabels) $params[self::$labels_param] = implode('|', $label);
			if($this->rotation) $params[self::$rotation_param] = $this->rotation;
		}
		return parent::Link($params);
	}

	function setColor($color1, $color2 = null) {
		$this->colors[0] = $color1;
		if($color2) $this->colors[1] = $color2;
	}

	function setRotation($rotation) {$this->rotation = $rotation;}
	
	function setShowLabels($show) {$this->showLabels = $show;}
}

/**
 * This class is used to create an interactive pie chart using javascript
 *
 * @link http://code.google.com/apis/chart/interactive/docs/gallery/piechart.html
 * @see PieChart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class PieChart_Interactive extends PieChart {
	
	static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	static $pie_slice_text_formats = array('percentage', 'value', 'label', 'none');
	
	protected $chartBorderColor, $chartBorderWidth;
	protected $chartDiameter;
	protected $fontSize, $fontName;
	protected $is3D;
	protected $legendFontName;
	protected $pieSliceText, $pieSliceColor, $pieSliceFontSize, $pieSliceFontName;
	protected $reverseCategories;
	protected $sliceVisibilityThreshold, $pieResidueSliceColor, $pieResidueSliceLabel;
	protected $titleFontName;
	protected $tooltipColor, $tooltipFontSize, $tooltipFontName;
	
	static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/pie.js');
	}
	
	function __construct() {
		parent::__construct();
		self::addRequirements();
	}
	
	function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"pie\"></div>";
	}
	
	function getJavascriptParams() {
		$params['id'] = "PI_$this->id";

		foreach($this->portions as $portion) {
			$titles[] = $portion['legend'];
			$values[] = intval($portion['value']);
			if(isset($portion['color'])) $colors[] = $portion['color'];
		}

		$params['titles'] = $titles;
		$params['values'] = $values;

		$options['width'] = $this->width ? $this->width : Chart::$default_width;
		$options['height'] = $this->height ? $this->height : Chart::$default_height;
		
		if($this->backgroundColor) $options['backgroundColor'] = $this->backgroundColor;
		if($this->chartBorderColor) $options['backgroundColor.stroke'] = $this->chartBorderColor;
		if($this->chartBorderWidth) $options['backgroundColor.strokeWidth'] = $this->chartBorderWidth;
		if($this->chartColor) $options['backgroundColor.fill'] = $this->chartColor;
		
		if(isset($colors)) $options['colors'] = $colors;
		
		if(isset($this->marginLeft)) $chartAreaOptions['left'] = $this->marginLeft;
		if(isset($this->marginTop)) $chartAreaOptions['top'] = $this->marginTop;
		if($this->chartDiameter) $chartAreaOptions['width'] = $chartAreaOptions['height'] = $this->chartDiameter;
		if(isset($chartAreaOptions)) {
			$options['chartArea'] = $chartAreaOptions;
		}
		
		if($this->fontSize) $options['fontSize'] = $this->fontSize;
		if($this->fontName) $options['fontName'] = $this->fontName;
		
		if($this->is3D) $options['is3D'] = 'true';
		
		if($this->legendPosition) $options['legend'] = $this->legendPosition;
		if($this->legendColor) $legendTextStyleOptions['color'] = $this->legendColor;
		if($this->legendFontSize) $legendTextStyleOptions['fontSize'] = $this->legendFontSize;
		if($this->legendFontName) $legendTextStyleOptions['fontName'] = $this->legendFontName;
		if(isset($legendTextStyleOptions)) {
			$options['legendTextStyle'] = $legendTextStyleOptions;
		}
		
		if($this->pieSliceText) $options['pieSliceText'] = $this->pieSliceText;
		if($this->pieSliceColor) $pieSliceTextStyleOptions['color'] = $this->pieSliceColor;
		if($this->pieSliceFontSize) $pieSliceTextStyleOptions['fontSize'] = $this->pieSliceFontSize;
		if($this->pieSliceFontName) $pieSliceTextStyleOptions['fontName'] = $this->pieSliceFontName;
		if(isset($pieSliceTextStyleOptions)) {
			$options['pieSliceTextStyle'] = $pieSliceTextStyleOptions;
		}
		
		if($this->reverseCategories) $options['reverseCategories'] = 'true';
		
		if($this->sliceVisibilityThreshold) $options['sliceVisibilityThreshold'] = $this->sliceVisibilityThreshold;
		if($this->pieResidueSliceColor) $options['pieResidueSliceColor'] = $this->pieResidueSliceColor;
		if($this->pieResidueSliceLabel) $options['pieResidueSliceLabel'] = $this->pieResidueSliceLabel;
		
		if($this->showTitle) {
			if($this->title) $options['title'] = $this->title;
			if($this->titleColor) $titleTextStyleOptions['color'] = $this->titleColor;
			if($this->titleSize) $titleTextStyleOptions['fontSize'] = $this->titleSize;
			if($this->titleFontName) $titleTextStyleOptions['fontName'] = $this->titleFontName;
			if(isset($titleTextStyleOptions)) {
				$options['titleTextStyle'] = $titleTextStyleOptions;
			}
		}
		
		if($this->tooltipColor) $tooltipTextStyleOptions['color'] = $this->tooltipColor;
		if($this->tooltipFontSize) $tooltipTextStyleOptions['fontSize'] = $this->tooltipFontSize;
		if($this->tooltipFontName) $tooltipTextStyleOptions['fontName'] = $this->tooltipFontName;
		if(isset($tooltipTextStyleOptions)) {
			$options['tooltipTextStyle'] = $tooltipTextStyleOptions;
		}
		
		$params['options'] = $options;
		return $params;
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawPieChart_Interactive($params);";
	}
	
	function setChartBorderColor($color) {$this->chartBorderColor = $color;}
	function setChartBorderWidth($width) {$this->chartBorderWidth = $width;}
	
	function setChartArea($left, $top, $diameter) {
		$this->marginLeft = $left;
		$this->marginTop = $top;
		$this->chartDiameter = $diameter;
	}
	
	function setFont($size, $name) {
		$this->fontSize = $size;
		$this->fontName = $name;
	}
	
	function enable3D() {$this->is3D = true;}
	
	function setLegendFontName($name) {$this->legendFontName = $name;}
	
	function setPieSliceText($format) {
		if(in_array($format, self::$pie_slice_text_formats)) {
			$this->pieSliceText = $format;
		}
	}
	
	function setPieSliceTextStyle($color, $fontName, $fontSize) {
		$this->pieSliceColor = $color;
		$this->pieSliceFontName = $fontName;
		$this->pieSliceFontSize = $fontSize;
	}
	
	function reverseCategories() {$this->reverseCategories = true;}
	
	function setSliceVisibilityThreshold($threshold) {$this->sliceVisibilityThreshold = $threshold;}
	function setPieResidueSliceColor($color) {$this->pieResidueSliceColor = $color;}
	function setPieResidueSliceLabel($label) {$this->pieResidueSliceLabel = $label;}
	
	function setTitleFontName($name) {$this->titleFontName = $name;}
	
	function setTooltipTextStyle($color, $fontName, $fontSize) {
		$this->tooltipColor = $color;
		$this->tooltipFontName = $fontName;
		$this->tooltipFontSize = $fontSize;
	}
}

?>
