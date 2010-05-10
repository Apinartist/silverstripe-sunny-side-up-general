<?php

class LineChart extends Chart {
	
	protected $type = 'lc';
	
	static $types = array('lc', 'ls', 'lxy');
	
	protected $lines = array();
	protected $factor;
	
	function addLine(array $y, array $x = null, $color = null, $legend = null) {
		$line = array('y' => $y);
		if($x) $line['x'] = $x;
		if($color) $line['color'] = $color;
		if($legend) $line['legend'] = $legend;
		$this->lines[] = $line;
	}
	
	function Link(array $params = null) {
		if(count($this->lines) > 0) {
			foreach($this->lines as $line) {
				if($this->type == 'lxy' && isset($line['x'])) $coordinates[] = implode(',', $line['x']);
				if($this->factor) {
					$y = array();
					foreach($line['y'] as $value) $y[] = $this->factor * $value;
					$coordinates[] = implode(',', $y);
				}
				else $coordinates[] = implode(',', $line['y']);
				if(isset($line['color'])) $colors[] = $line['color'];
				else if($this->generateColor) $colors[] = Chart::get_hexa_color();
				if(isset($line['legend'])) $legend[] = str_replace(' ', '+', $line['legend']);
			}
			$params[Chart::$data_param] = 't:' . implode('|', $coordinates);
			if(isset($colors)) $params[Chart::$color_param] = implode(',', $colors);
			if(isset($legend)) $params[$this->stat('legend_labels_param')] = implode('|', $legend);
		}
		return parent::Link($params);
	}
	
	function setFactor($factor) {$this->factor = $factor;}
}

?>