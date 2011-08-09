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

	/**
	 * A list of valid chart types.
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#chart_types
	 * @var array
	 */
	public static $types = array(
		'p', 					//	A standard two dimensional pie chart (default)
		'p3', 				//	A three-dimensional pie chart
		'pc' 					//	A concentric pie chart (supply two or more data series)
	);
	
	/**
	 * The name/key of the in-chart label toggle parameter
	 *
	 * @var string
	 */
	public static $labels_param = 'chl';
	
	/**
	 * The name/key of the chart rotation parameter
	 *
	 * @var string
	 */
	public static $rotation_param = 'chp';

	/**
	 * An array of portions/slices in the pie.  Each item is an associative array of properties
	 *
	 * @var array
	 */
	protected $portions = array();

	/**
	 * The current chart type.  Must be one of the values in BarChart::$types
	 *
	 * @var string
	 */
	protected $type = 'p';

	/**
	 * An array of one or two colours that will be used for pie slices
	 *
	 * @var array
	 */
	protected $colors = array();
	
	/**
	 * The starting angle in radians
	 *
	 * @var int
	 */
	protected $rotation;
	
	/**
	 * Whether to show in-chart labels or not
	 *
	 * @var bool
	 */
	protected $showLabels = true;
	
	
	/**
	 * Returns a URL to this chart image on Google Charts
	 * 
	 * @param array $params An optional set of default chart parameters (eg for setting params in subclasses).  Will be overwritten by properties set on this chart)
	 * @return string
	 */
	public function Link(array $params = null) {
	
		if(count($this->portions) > 0) {
		
			foreach($this->portions as $portion) {
				$portions[] = $portion['value'];
				
				if(isset($portion['color'])) {
					$colors[] = str_replace('#', '', $portion['color']);
				} else if($this->generateColor) {
					$colors[] = Chart::get_hexa_color();
				}
				
				if(isset($portion['legend'])) {
					$legend[] = str_replace(' ', '+', $portion['legend']);
				}
				if(isset($portion['label'])) {
					$label[] = str_replace(' ', '+', $portion['label']);
				}
			}
			
			$total = array_sum($portions);
			if($total == 0) {
				$total = 1;
			}
			foreach($portions as $i => $portion) {
				$portions[$i] = round($portion * 100 / $total);
			}
			
			$params[Chart::$data_param] = 't:' . implode(',', $portions);
			
			
			
			if(isset($colors)) {
				$params[Chart::$color_param] = implode(count($colors) == count($this->portions) ? '|' : ',', $colors);
			} else if(count($this->colors) > 0) {
				$params[Chart::$color_param] = implode(',', $this->colors);
			}
			
			if(isset($legend) && $this->showLegend) {
				$params[Chart::$legend_labels_param] = implode('|', $legend);
			}
			if(isset($label) && $this->showLabels) {
				$params[self::$labels_param] = implode('|', $label);
			}
			if($this->rotation) {
				$params[self::$rotation_param] = $this->rotation;
			}
						
		}
		return parent::Link($params);
	}
	

	/**
	 * Add a slice to the pie
	 * 
	 * @param string $portion The data value of this slice.  The actual size of the portion will be calculated based on the total value of all slices 
	 * @param string $colour The slice colour in #RRGGBB format, or null to use setColor() or generateColor()
	 * @param string $legend The legend label for this slice
	 * @param string $label The in-chart label for this slice
	 */
	public function addPortion($portion, $color = null, $legend = null, $label = null) {
		$portion = array('value' => $portion);
		if($color) {
			$portion['color'] = $color;
		}
		if($legend) {
			$portion['legend'] = $legend;
		}
		if($label) {
			$portion['label'] = $label;
		}
		$this->portions[] = $portion;
	}

	/**
	 * Define one or two colours that will be used for pie slices (extra slices will have colours between these two).  Not used if colour is set in {@see addPortion()}
	 * 
	 * @param string $color1
	 * @param string $color2
	 */
	public function setColor($color1, $color2 = null) {
		$this->colors[0] = $color1;
		if($color2) {
			$this->colors[1] = $color2;
		}
	}



	/**
	 * Define the angle to start drawing slices from.  By default the first series is drawn starting at 3:00, continuing clockwise around the chart.
	 * 
	 * @link http://code.google.com/apis/chart/image/docs/gallery/pie_charts.html#pie_chart_rotation
	 * @param int $rotation In radians
	 */
	public function setRotation($rotation) {
		$this->rotation = $rotation;
	}
	
	/**
	 * Set whether to show on-chart labels (attached to slices by pointing lines).  Defaults to true.
	 * 
	 * @param bool $show
	 */
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
	
	/**
	 * Extensions used by this class
	 *
	 * @var array
	 */
	public static $extensions = array('InteractiveChart');
	
	/**
	 * An array of allowed legend position codes
	 *
	 * @var array
	 */
	public static $legend_positions = array('left', 'right', 'top', 'bottom', 'none');
	
	
	public static $pie_slice_text_formats = array('percentage', 'value', 'label', 'none');
	

	
	protected $is3D;
	protected $pieSliceText, $pieSliceColor, $pieSliceFontSize, $pieSliceFontName;
	protected $sliceVisibilityThreshold, $pieResidueSliceColor, $pieResidueSliceLabel;
	
	
	/**
	 * Includes the needed JS files via the Requirements system
	 */
	public static function addRequirements() {
		Requirements::javascript('http://www.google.com/jsapi');
		Requirements::javascript('googlecharts/javascript/pie.js');
	}
	
	/**
	 * 
	 */
	public function __construct() {
		parent::__construct();
		self::addRequirements();
	}
	
	/**
	 * Standard SS method
	 * 
	 * @return string A <div> tag and the javascript needed to convert it to a graph
	 */
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"pie\"></div>";
	}
	
	/**
	 * Returns the JS function call that generates the chart in string format
	 * 
	 * @return string
	 */
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		$params = Convert::array2json($params);
		return "drawPieChart_Interactive($params);";
	}
	
	/**
	 * Returns an array of parameters that will be passed to the JS chart.draw() function
	 * 
	 * @return array An associative array of parameters
	 */
	public function getJavascriptParams() {
		$params['id'] = "PI_$this->id";
		
		foreach($this->portions as $portion) {
			$titles[] = $portion['legend'];
			$values[] = intval($portion['value']);
			if(isset($portion['color'])) {
				$colors[] = $portion['color'];
			}
		}
		
		$params['titles'] = $titles;
		$params['values'] = $values;
		
		if(isset($colors)) {
			$options['colors'] = $colors;
		}
		
		if($this->is3D) {
			$options['is3D'] = 'true';
		}
		
		if($this->pieSliceText) {
			$options['pieSliceText'] = $this->pieSliceText;
		}
		if($this->pieSliceColor) {
			$pieSliceTextStyleOptions['color'] = $this->pieSliceColor;
		}
		if($this->pieSliceFontSize) {
			$pieSliceTextStyleOptions['fontSize'] = $this->pieSliceFontSize;
		}
		if($this->pieSliceFontName) {
			$pieSliceTextStyleOptions['fontName'] = $this->pieSliceFontName;
		}
		if(isset($pieSliceTextStyleOptions)) {
			$options['pieSliceTextStyle'] = $pieSliceTextStyleOptions;
		}
		
		if($this->sliceVisibilityThreshold) {
			$options['sliceVisibilityThreshold'] = $this->sliceVisibilityThreshold;
		}
		if($this->pieResidueSliceColor) {
			$options['pieResidueSliceColor'] = $this->pieResidueSliceColor;
		}
		if($this->pieResidueSliceLabel) {
			$options['pieResidueSliceLabel'] = $this->pieResidueSliceLabel;
		}
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		return $params;
	}
	
	
	/**
	 * Make this a 3D chart
	 */
	public function enable3D() {
		$this->is3D = true;
	}
	
	/**
	 * Set the formatting for the text labels on each slice
	 * 
	 * @param string $format Must be one of the values in {@see $legend_positions}
	 */
	public function setPieSliceText($format) {
		if(in_array($format, self::$pie_slice_text_formats)) {
			$this->pieSliceText = $format;
		}
	}
	
	/**
	 * Style the text used use for slice labels
	 * 
	 * @param string $color The text colour in #RRGGBB format
	 * @param string $fontName The name of the font to use
	 * @param int $fontSize The sice of the font in pixels
	 */
	public function setPieSliceTextStyle($color, $fontName, $fontSize) {
		$this->pieSliceColor = $color;
		$this->pieSliceFontName = $fontName;
		$this->pieSliceFontSize = $fontSize;
	}
	
	/**
	 * All slices below the given threshold will be merged in to a single "other" category instead of shown individually
	 * 
	 * @param float $threshold 1 = 100% -all slices will be merged, 0 = 0% = no slices will be merged
	 */
	public function setSliceVisibilityThreshold($threshold) {
		$this->sliceVisibilityThreshold = $threshold;
	}
	
	/**
	 * The colour of the "other" slice ({@see setSliceVisibilityThreshold})
	 * 
	 * @param string $color The colour in #RRGGBB format
	 */
	public function setPieResidueSliceColor($color) {
		$this->pieResidueSliceColor = $color;
	}
	
	/**
	 * The legend label of the "other" slice ({@see setSliceVisibilityThreshold})
	 * 
	 * @param string $label The label text
	 */
	public function setPieResidueSliceLabel($label) {
		$this->pieResidueSliceLabel = $label;
	}
}


