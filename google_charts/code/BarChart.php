<?php

/**
 * This class is used to create a bar chart as an image using google charts
 * 
 * @link http://code.google.com/apis/chart/docs/gallery/bar_charts.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class BarChart extends Chart {
	
	protected $type = 'bvs';
	
	static $types = array('bhs', 'bvs', 'bvo', 'bhg', 'bvg');
	static $bar_width_spacing_param = 'chbh';
	
	protected $series = array();
	protected $barWidthScale;
	protected $barSpace;
	protected $barGroupSpace;
	
	function addSerie(array $y, $color = null, $legend = null) {
		$serie = array('y' => $y);
		if($color) $serie['color'] = $color;
		if($legend) $serie['legend'] = $legend;
		$this->series[] = $serie;
	}
	
	function Link(array $params = null) {
		if(count($this->series) > 0) {
			foreach($this->series as $i => $serie) {
				$series[] = implode(',', $serie['y']);
				if(isset($serie['color'])) $colors[] = is_array($serie['color']) ? implode('|', $serie['color']) : $serie['color'];
				else if($this->generateColor) $colors[] = Chart::get_hexa_color();
				if(isset($serie['legend'])) {
					if(! is_array($serie['legend'])) $serie['legend'] = array($serie['legend']);
					foreach($serie['legend'] as $legend) $legends[] = str_replace(' ', '+', $legend);
				}
			}
			
			$params[Chart::$data_param] = 't:' . implode('|', $series);
			if(isset($colors)) $params[Chart::$color_param] = implode(',', $colors);
			if(isset($legends)) $params[Chart::$legend_labels_param] = implode('|', $legends);
			if($this->barWidthScale) $params[self::$bar_width_spacing_param] = "$this->barWidthScale,$this->barSpace,$this->barGroupSpace";
		}
		return parent::Link($params);
	}
	
	function setBarWidthAndSpacing($widthScale, $barSpace = null, $barGroupSpace = null) {
		if($widthScale == 'a' || $widthScale == 'r' || is_number($widthScale)) {
			$this->barWidthScale = $widthScale;
			if($barSpace != null) $this->barSpace = $barSpace;
			if($barGroupSpace != null) $this->barGroupSpace = $barGroupSpace;
		}
	}
}

?>