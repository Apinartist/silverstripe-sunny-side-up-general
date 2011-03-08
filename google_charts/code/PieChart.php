<?php

/**
 * This class is used to create a pie chart as an image using google charts
 *
 * @link http://code.google.com/apis/chart/docs/gallery/pie_charts.html
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
				if(isset($portion['color'])) $colors[] = $portion['color'];
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
			if(isset($legend)) $params[Chart::$legend_labels_param] = implode('|', $legend);
			if(isset($label)) $params[self::$labels_param] = implode('|', $label);
			if($this->rotation) $params[self::$rotation_param] = $this->rotation;
		}
		return parent::Link($params);
	}

	function setColor($color1, $color2 = null) {
		$this->colors[0] = $color1;
		if($color2) $this->colors[1] = $color2;
	}

	function setRotation($rotation) {$this->rotation = $rotation;}
}

/**
 * This class is used to create an interactive pie chart using javascript
 *
 * @link http://code.google.com/apis/visualization/documentation/gallery/piechart.html
 * @see PieChart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class PieChart_Interactive extends PieChart {

	static $legend_positions = array('left', 'right', 'none');

	protected $backgroundColor;
	protected $fontSize;

	protected $legendFontSize, $legendColor;
	protected $reverseCategories;
	protected $tooltipFontSize;
	
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

		if(isset($colors)) $options['colors'] = $colors;

		if($this->title) $options['title'] = $this->title;
		if($this->titleColor) $options['titleTextColor'] = $this->titleColor;
		if($this->titleSize) $options['titleFontSize'] = $this->titleSize;

		if($this->backgroundColor) $options['backgroundColor'] = $this->backgroundColor;
		if($this->fontSize) $options['fontSize'] = $this->fontSize;

		if($this->legendPosition) $options['legend'] = $this->legendPosition;
		if($this->legendFontSize) $options['legendFontSize'] = $this->legendFontSize;
		if($this->legendColor) $options['legendTextColor'] = $this->legendColor;

		if($this->reverseCategories) $options['reverseCategories'] = 'true';

		if($this->tooltipFontSize) $options['tooltipFontSize'] = $this->tooltipFontSize;

		$params['options'] = $options;
		return $params;
	}
	
	function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "draw{$this->class}($params);";
	}
	
	function setBackgroundColor($color) {$this->backgroundColor = $color;}
	function setFontSize($size) {$this->fontSize = $size;}

	function setLegendFontSize($size) {$this->legendFontSize = $size;}
	function setLegendColor($color) {$this->legendColor = $color;}

	function reverseCategories() {$this->reverseCategories = true;}

	function setTooltipFontSize($size) {$this->tooltipFontSize = $size;}
}

?>
