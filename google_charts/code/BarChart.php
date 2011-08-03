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
	public static $types = array('bhs', 'bvs', 'bvo', 'bhg', 'bvg');
	public static $bar_width_spacing_param = 'chbh';
	
	protected $barWidthScale;
	protected $barSpace;
	protected $barGroupSpace;
	protected $type = 'bvs';
	
	
	public function Link(array $params = null) {
		if(count($this->lines) > 0) {
			if($this->barWidthScale) {
				$scale = $this->barWidthScale;
				if($this->barSpace) $scale .= ",$this->barSpace";
				else if($this->barGroupSpace) $scale .= ',1';
				if($this->barGroupSpace) $scale .= ",$this->barGroupSpace";
				$params[self::$bar_width_spacing_param] = $scale;
			}
			if($this->markerType) {
				foreach($this->lines as $i => $serie) {
					$markers[] = "$this->markerType,$this->markerColor,$i,,$this->markerSize";
				}
				$params[self::$marker_param] = implode('|', $markers);
				if(isset($this->axisRange[1])) {
					$params['chds'] = implode(',', $this->axisRange[1]);
				}
			}
		}
		return parent::Link($params);
	}
	
	
	public function addLine() {
		user_error('You can not use the \'addLine\' function on a bar/column chart. Use the \'addSerie\' function instead.');
	}
	
	public function addSerie(array $y, $color = null, $legend = null) {
		parent::addLine($y, null, $color, $legend);
	}
	

	
	public function setBarWidthAndSpacing($widthScale, $barSpace = null, $barGroupSpace = null) {
		if($widthScale == 'a' || $widthScale == 'r' || is_numeric($widthScale)) {
			$this->barWidthScale = $widthScale;
			if($barSpace != null) $this->barSpace = $barSpace;
			if($barGroupSpace != null) $this->barGroupSpace = $barGroupSpace;
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
	
	public static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	
	public static $extensions = array('InteractiveChart', 'InteractiveChart_Axis');
	
	public static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/bar.js');
		Requirements::javascript('googlecharts/javascript/tooltipfix.js');
	}
	
	public function __construct() {
		parent::__construct();
		self::addRequirements();
	}
	
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"bar\"></div>";
	}
	
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawBarChart_Interactive($params);";
	}
	
	public function getJavascriptParams() {
		$params['id'] = "BA_$this->id";
		
		foreach($this->lines as $serie) {
			$yTitles[] = $serie['legend'];
			$values[] = array_map('floatval', $serie['y']);
			if(isset($serie['color'])) $colors[] = $serie['color'];
		}
		
		$params['xTitles'] = $this->axisLabels[0];
		$params['yTitles'] = $yTitles;
		$params['values'] = $values;
		$params['type'] = $this->getInteractiveType();
		
		if(isset($colors)) $options['colors'] = $colors;
		
		$options['isStacked'] = $this->isStacked() ? 'true' : 'false';
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		return $params;
	}
	
	public function getInteractiveType() {
		return $this->type[1] == 'h' ? 'Bar' : 'Column';
	}
	
	public function isStacked() {
		return $this->type[2] != 'g';
	}
}

