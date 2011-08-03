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


	public static $types = array('p', 'p3', 'pc');
	public static $labels_param = 'chl';
	public static $rotation_param = 'chp';

	protected $portions = array();

	protected $type = 'p';

	protected $colors = array();
	protected $rotation;
	protected $showLabels = true;

	public function Link(array $params = null) {
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
	

	public function addPortion($portion, $color = null, $legend = null, $label = null) {
		$portion = array('value' => $portion);
		if($color) $portion['color'] = $color;
		if($legend) $portion['legend'] = $legend;
		if($label) $portion['label'] = $label;
		$this->portions[] = $portion;
	}

	public function setColor($color1, $color2 = null) {
		$this->colors[0] = $color1;
		if($color2) $this->colors[1] = $color2;
	}

	public function setRotation($rotation) {
		$this->rotation = $rotation;
	}
	
	public function setShowLabels($show) {
		$this->showLabels = $show;
	}
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
	
	public static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	public static $pie_slice_text_formats = array('percentage', 'value', 'label', 'none');
	public static $extensions = array('InteractiveChart');
	
	protected $is3D;
	protected $pieSliceText, $pieSliceColor, $pieSliceFontSize, $pieSliceFontName;
	protected $sliceVisibilityThreshold, $pieResidueSliceColor, $pieResidueSliceLabel;
	
	
	
	public static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/pie.js');
	}
	
	public function __construct() {
		parent::__construct();
		self::addRequirements();
	}
	
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"pie\"></div>";
	}
	
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawPieChart_Interactive($params);";
	}
	
	public function getJavascriptParams() {
		$params['id'] = "PI_$this->id";
		
		foreach($this->portions as $portion) {
			$titles[] = $portion['legend'];
			$values[] = intval($portion['value']);
			if(isset($portion['color'])) $colors[] = $portion['color'];
		}
		
		$params['titles'] = $titles;
		$params['values'] = $values;
		
		if(isset($colors)) $options['colors'] = $colors;
		
		if($this->is3D) $options['is3D'] = 'true';
		
		if($this->pieSliceText) $options['pieSliceText'] = $this->pieSliceText;
		if($this->pieSliceColor) $pieSliceTextStyleOptions['color'] = $this->pieSliceColor;
		if($this->pieSliceFontSize) $pieSliceTextStyleOptions['fontSize'] = $this->pieSliceFontSize;
		if($this->pieSliceFontName) $pieSliceTextStyleOptions['fontName'] = $this->pieSliceFontName;
		if(isset($pieSliceTextStyleOptions)) {
			$options['pieSliceTextStyle'] = $pieSliceTextStyleOptions;
		}
		
		if($this->sliceVisibilityThreshold) $options['sliceVisibilityThreshold'] = $this->sliceVisibilityThreshold;
		if($this->pieResidueSliceColor) $options['pieResidueSliceColor'] = $this->pieResidueSliceColor;
		if($this->pieResidueSliceLabel) $options['pieResidueSliceLabel'] = $this->pieResidueSliceLabel;
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		return $params;
	}
	
	public function enable3D() {
		$this->is3D = true;
	}
	
	public function setPieSliceText($format) {
		if(in_array($format, self::$pie_slice_text_formats)) {
			$this->pieSliceText = $format;
		}
	}
	
	public function setPieSliceTextStyle($color, $fontName, $fontSize) {
		$this->pieSliceColor = $color;
		$this->pieSliceFontName = $fontName;
		$this->pieSliceFontSize = $fontSize;
	}
	
	public function setSliceVisibilityThreshold($threshold) {
		$this->sliceVisibilityThreshold = $threshold;
	}
	
	public function setPieResidueSliceColor($color) {
		$this->pieResidueSliceColor = $color;
	}
	
	public function setPieResidueSliceLabel($label) {
		$this->pieResidueSliceLabel = $label;
	}
}


