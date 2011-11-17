<?php

/**
 * This class is used to create a map chart as an image using google charts
 * 
 * @link http://code.google.com/apis/chart/image/docs/gallery/new_map_charts.html
 * @link http://code.google.com/apis/chart/image/docs/gallery/map_charts.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class MapChart extends Chart {
	
	public static $max_dimensions = array(
		't' => array(400, 220),
		'map' => array(600, 500)
	);
	
	public static $area_centering = array(
		'world' => array(-70, -170, 85, 180)
	);
	
	public static $types = array('t', 'map');	
	
	public static $area_param = 'chtm';
	public static $country_param = 'chld';
	
	public static $areas = array('africa', 'asia', 'europe', 'middle_east', 'south_america', 'usa', 'world');
	
	protected $type = 't';
	protected $area = 'world';
	
	protected $records = array();
	protected $colors = array();
	
	protected $centered = false;
	
	protected $positionLeft, $positionRight, $positionTop, $positionBottom; 
	
	
	public function Link(array $params = null) {
		$params[self::$area_param] = $this->area;
		if(count($this->records) > 0) {
			$params[self::$country_param] = implode($this->type == 't' ? '' : '|', array_keys($this->records));
			$records = array_values($this->records);
			foreach($records as $i => $fields) {
				$values[] = $fields[0];
				if(isset($fields[1]) && $this->markerType) {
					$marker = $text = $fields[1];
					$offset = '';
					$repeat = false;
					if(is_array($marker)) {
						list($text, $offsets, $repeat) = $marker;
						$offset = ',,:' . implode(':', $offsets);
					}
					$text = urlencode($text);
					$markers[] = "$this->markerType$text,$this->markerColor,0,$i,$this->markerSize$offset";
					if($repeat) {
						$j = count($records) + (isset($extraMarkers) ? count($extraMarkers) : 0);
						$extraMarkers[] = "$this->markerType$text,$this->markerColor,0,$j,$this->markerSize$offset";
					}
				}
			}
			$params[Chart::$data_param] = 't:' . implode(',', $values);
			if(isset($markers)) {
				if(isset($extraMarkers)) {
					$markers = array_merge($markers, $extraMarkers);
				}
				$params[self::$marker_param] = implode('|', $markers);
			}
		}
		if(count($this->colors) > 0) $params[Chart::$color_param] = implode(',', $this->colors);
		return parent::Link($params);
	}
	
	public function addRecord($title, $value, $marker = null) {
		$this->records[$title] = array(floatval($value));
		if($marker) $this->records[$title][] = $marker;
	}
	
	public function getTypeForLink() {
		$type = parent::getTypeForLink();
		if($type == 'map') {
			if($this->centered && isset(self::$area_centering[$this->area])) {
				$type .= ":fixed=" . implode(',', self::$area_centering[$this->area]);
			}
			else if(isset($this->positionTop)) {
				$type .= ":auto=$this->positionLeft,$this->positionRight,$this->positionTop,$this->positionBottom";
			}
		}
		return $type;
	}
	
	public function getFinalWidth() {
		$width = parent::getFinalWidth();
		return isset(self::$max_dimensions[$this->type]) ? min($width, self::$max_dimensions[$this->type][0]) : $width;
	}
	
	public function getFinalHeight() {
		$height = parent::getFinalHeight();
		return isset(self::$max_dimensions[$this->type]) ? min($height, self::$max_dimensions[$this->type][1]) : $height;
	}
	
	public function setColors(array $colors) {
		$this->colors = $colors;
	}
	
	public function setPosition($left, $right, $top, $bottom) {
		$this->positionLeft = $left;
		$this->positionRight = $right;
		$this->positionTop = $top;
		$this->positionBottom = $bottom;
	}
	
	public function center() {
		$this->centered = true;
	}
}

/**
 * This class is used to create an interactive map chart using javascript
 * 
 * @link http://code.google.com/apis/chart/interactive/docs/gallery/geomap.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class MapChart_Geo extends MapChart {

	/**
	 * Extensions used by this class
	 *
	 * @var array
	 */
	public static $extensions = array('InteractiveChart');
	
	public static $types = array('regions', 'markers');
	
	public static $regions = array('world', 'us_metro',
		'005', // South America
		'013', // Central America
		'021', // North America
		'002', // All Africa
		'017', // Central Africa
		'015', // North Africa
		'018', // South Africa
		'030', // East Asia
		'034', // South Asia
		'035', // Asia Pacific
		'143', // Central Asia
		'145', // Middle East
		'151', // North Asia
		'154', // North Europe
		'155', // West Europe
		'039'  // South Europe
	);
	
	protected $type = 'regions';
	protected $region = 'world';
	
	
	public static function addRequirements() {
		$s = Chart::$secure_requirements ? 's' : '';
		Requirements::javascript("http$s://www.google.com/jsapi");
		Requirements::javascript('googlecharts/javascript/geomap.js');
	}
	
	public function __construct() {
		parent::__construct();
		self::addRequirements();
	}

	
	public function forTemplate() {
		$params = $this->getJavascriptParams();
		
		$script = $this->getJavascript();
		Requirements::customScript("google.setOnLoadCallback(function() {{$script}});");
		return "<div id=\"{$params['id']}\" class=\"geomap\"></div>";
	}
	
	public function setRegion($region) {
		if(in_array($region, self::$regions) || in_array($region, array_keys(Geoip::getCountryDropDown()))) {
			$this->region = $region;
		}
	}	
	
	
	public function getJavascriptParams() {
		$params = array(
			'id' => "GM_$this->id",
			'records' => array_keys($this->records)
		);
		
		foreach($this->records as $record) {
			$totals[] = floatval($record[0]);
		}
		$params['totals'] = $totals;
		
		if($this->showTitle && $this->title) {
			$params['title'] = $this->title;
		}

		$options = array(
			'width' => $this->getFinalWidth(),
			'height' => $this->getFinalHeight(),
			'dataMode' => $this->type,
			'region' => $this->region
		);
		
		
		if(count($this->colors) > 0) $options['colors'] = $this->colors;
		if(isset($this->showLegend)) $options['showLegend'] = $this->showLegend;
		
		
		$this->extend('updateJavascriptParams', $options);
		
		$params['options'] = $options;
		
		return $params;
	}
	
	public function getJavascript() {
		$params = $this->getJavascriptParams();
		
		$params = Convert::array2json($params);
		return "drawMapChart_Geo($params);";
	}
}
