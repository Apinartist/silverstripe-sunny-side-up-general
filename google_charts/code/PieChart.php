<?php

class PieChart extends Chart {
	
	protected $type = 'p';
	
	static $types = array('p', 'p3', 'pc');
	
	protected $portions = array();
	
	static $legend_labels_param = 'chl';
	static $rotation_param = 'chp';
	
	protected $colors = array();
	protected $rotation;
	
	function addPortion($portion, $color = null, $legend = null) {
		$portion = array('value' => $portion);
		if($color) $portion['color'] = $color;
		if($legend) $portion['legend'] = $legend;
		$this->portions[] = $portion;
	}
	
	function Link(array $params = null) {
		if(count($this->portions) > 0) {
			foreach($this->portions as $portion) {
				$portions[] = $portion['value'];
				if(isset($portion['color'])) $colors[] = $portion['color'];
				else if($this->generateColor) $colors[] = Chart::get_hexa_color();
				if(isset($portion['legend'])) $legend[] = str_replace(' ', '+', $portion['legend']);
			}
			$params[Chart::$data_param] = 't:' . implode(',', $portions);
			if(isset($colors)) $params[Chart::$color_param] = implode(count($colors) == count($this->portions) ? '|' : ',', $colors);
			else if(count($this->colors) > 0) $params[Chart::$color_param] = implode(',', $this->colors);
			if(isset($legend)) $params[$this->stat('legend_labels_param')] = implode('|', $legend);
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

?>