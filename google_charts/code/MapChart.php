<?php

/**
 * This class is used to create a map chart as an image using google charts
 * 
 * @link http://code.google.com/apis/chart/docs/gallery/map_charts.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class MapChart extends Chart {
	
	protected $width = 440;
	protected $height = 220;
	
	protected $type = 't';
	protected $area = 'world';
	
	static $types = array('t');
	
	protected $countries = array();
	protected $colors = array();
	
	static $area_param = 'chtm';
	static $country_param = 'chld';
	
	static $areas = array('africa', 'asia', 'europe', 'middle_east', 'south_america', 'usa', 'world');
	
	function addCountry($country, $value) {$this->countries[$country] = $value;}
	
	function Link(array $params = null) {
		$params[self::$area_param] = $this->area;
		if(count($this->countries) > 0) {
			$params[self::$country_param] = implode('', array_keys($this->countries));
			$params[Chart::$data_param] = 't:' . implode(',', $this->countries);
		}
		if(count($this->colors) > 0) $params[Chart::$color_param] = implode(',', $this->colors);
		return parent::Link($params);
	}
	
	function setColor($color1, $color2 = null) {
		$this->colors[0] = $color1;
		if($color2) $this->colors[1] = $color2;
	}
}

/**
 * This class is used to create an interactive map chart using javascript
 * 
 * @link http://code.google.com/apis/visualization/documentation/gallery/geomap.html
 * @see Chart
 * @package googlecharts
 * @author Romain Louis <romain@sunnysideup.co.nz>
 */
class MapChart_Geo extends Chart {
	
	protected $type = 'regions';
	protected $region = 'world';
	protected $title = 'Popularity';
	
	static $types = array('regions', 'markers');
	
	static $regions = array('world', 'us_metro',
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
	
	protected $hideLegend;
	protected $records = array();
	
	function __construct() {
		parent::__construct();
		Requirements::javascript('http://www.google.com/jsapi');
	}
	
	function addRecord($title, $value) {$this->records[$title] = $value;}
	
	function setRegion($region) {
		if(in_array($region, self::$regions) || in_array($region, array_keys(Geoip::getCountryDropDown()))) $this->region = $region;
	}
	
	function forTemplate() {
		$id = "GM_$this->id";
		$params['id'] = $id;
		
		$params['records'] = "'" . implode("', '", array_keys($this->records)) . "'";
		$params['totals'] = implode(', ', $this->records);
		$params['title'] = $this->title;
		
		$options[] = 'width: ' . ($this->width ? $this->width : Chart::$default_width);
		$options[] = 'height: ' . ($this->height ? $this->height : Chart::$default_height);
		
		$options[] = "dataMode: '$this->type'";
		$options[] = "region: '$this->region'";
		
		if($this->colors) $options[] = "colors: ['" . (implode("', '", $this->colors)) . "']";
		if($this->hideLegend) $options[] = 'showLegend: false';
		
		$params['options'] = implode(', ', $options);
		Requirements::javascriptTemplate('googlecharts/javascript/geomap.js', $params);
		return "<div id=\"$id\" class=\"geomap\"></div>";
	}
	
	function setColor($color1, $color2 = null) {
		$this->colors[0] = "0x$color1";
		if($color2) $this->colors[1] = "0x$color2";
	}
	
	function hideLegend() {$this->hideLegend = true;}
}

?>