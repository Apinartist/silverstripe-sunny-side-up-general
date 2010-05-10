<?php

class ChartData extends Object {
	
	public $y, $x;
	public $color;
	public $legend;
	
	function __construct(array $y, array $x = null, $color = null, $legend = null) {
		parent::__construct();
		$this->y = $y;
		$this->x = $x;
		$this->color = $color;
		$this->legend = $legend;
	}
}

?>