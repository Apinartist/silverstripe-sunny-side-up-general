<?php
/**
 * This object is a highly customisable object
 * it requires google data points as input
 * it can load a google map to your page and return datapoints as XML
 * you implement it in a page controller by adding:
 * function init() { $m = new GoogleMap(); $m->loadGoogleMap($this->URLSegment."/returnXMLData")}
 * function returnXMLData() { $this->renderWith("GoogleMapXml"); $m = new GoogleMap(); $m->setDataObject(...); return $m->getXmlDataPoints; }
 * SHOULD BE CHANGE TO RequestHandlingData *************** see http://pastie.org/292754
	*/
class GoogleMap extends ViewableData {

	static $includesDone = false;// this is a hack to avoid having multiple includes
	/* INFORMATION AROUND THE MAP */
	protected static $DefaultTitle = "";
		static function setDefaultTitle($v){self::$DefaultTitle = $v;}

	protected static $NoStatusAtAll = false;
		static function setNoStatusAtAll($v) {self::$NoStatusAtAll = $v;}
	protected static $AddKmlLink = false;
		static function setAddKmlLink($v){self::$AddKmlLink = $v;}
	protected static $HiddenLayersRemovedFromList = false;
		static function setHiddenLayersRemovedFromList($v){self::$HiddenLayersRemovedFromList = $v;}
	protected static $ChangePageTitle = false;
		static function setChangePageTitle($v){self::$ChangePageTitle = $v;}

	/* DIVS */
	protected static $TitleDivId = "";
		static function setTitleDivId($v) {self::$TitleDivId = $v;}
		public function getTitleDivID() {return self::$TitleDivId;}
	protected static $SideBarDivId = "";
		static function setSideBarDivId($v){self::$SideBarDivId = $v; }
		public function getSideBarDivId() {return self::$SideBarDivId;}
	protected static $DropDownDivId	="";
		static function setDropDownDivId	($v) {self::$DropDownDivId = $v; }
		public function getDropDownDivId() {return self::$DropDownDivId;}
	protected static $LayerListDivId = "";
		static function setLayerListDivId($v) {self::$LayerListDivId = $v; }
		public function getLayerListDivId() {return self::$LayerListDivId;}
	protected static $DirectionsDivId = "";
		static function setDirectionsDivId($v) {self::$DirectionsDivId = $v; }
		public function getDirectionsDivId() {return self::$DirectionsDivId;}
	protected static $StatusDivId = "";
		static function setStatusDivId($v) {self::$StatusDivId = $v; }
		public function getStatusDivId() {return self::$StatusDivId;}


	/* INFOWINDOW*/
	protected static $InfoWindowOptions = "{maxWidth:280, zoomLevel:17, mapType:G_HYBRID_MAP}";
		static function setInfoWindowOptions($v) {self::$InfoWindowOptions = $v;}
	protected static $AddAntipodean = false;
		static function setAddAntipodean($v) {self::$AddAntipodean = $v;}
	protected static $AddDirections = false;
		static function setAddDirections($v) {self::$AddDirections = $v;}
	protected static $AddCurrentAddressFinder = false;
		static function setAddCurrentAddressFinder($v) {self::$AddCurrentAddressFinder = $v;}


	/* MARKERS */
	protected static $AddPointsToMap = false;
		static function setAddPointsToMap($v) {self::$AddPointsToMap = $v;}
	protected static $MarkerOptions = "{bouncy:true,title: \"click me\"}";
		static function setMarkerOptions($v) {self::$MarkerOptions = $v;}
	protected static $PreloadImages = false;
		static function setPreloadImages($v) {self::$PreloadImages = $v;}

	/* ICONS */
	protected static $DefaultIconUrl = "";
		static function setDefaultIconUrl($v) {self::$DefaultIconUrl = $v;}
	protected static $IconFolder = "/googleMap/images/icons/";
		static function setIconFolder($v) {self::$IconFolder = $v;}
	protected static $IconWidth = 20;
		static function setIconWidth($v) {self::$IconWidth = $v;}
	protected static $IconHeight = 34;
		static function setIconHeight($v) {self::$IconHeight = $v;}
	protected static $IconExtension = "png";
		static function setIconExtension($v) {self::$IconExtension = $v;}
	protected static $IconMaxCount = 12;
		static function setIconMaxCount($v) {self::$IconMaxCount = $v;}

	/* POLYS */
	protected static $LineColour = "#000";
		static function setLineColour($v) {self::$LineColour = $v;}
	protected static $LineWidth = 12;
		static function setLineWidth($v) {self::$LineWidth = $v;}
	protected static $LineOpacity = 0.5;
		static function setLineOpacity($v) {self::$LineOpacity = $v;}
	protected static $FillColour = "#ffccff";
		static function setFillColour($v) {self::$FillColour = $v;}
	protected static $FillOpacity = 0.5;
		static function setFillOpacity($v) {self::$FillOpacity = $v;}
	protected static $PolyIcon = "";
		static function setPolyIcon($v) {self::$PolyIcon = $v;}

	/* MAP*/
	protected static $GoogleMapWidth = 500;
		static function setGoogleMapWidth($v) {self::$GoogleMapWidth = $v; }
		public function getGoogleMapWidth() {return self::$GoogleMapWidth;}
	protected static $GoogleMapHeight = 500;
		static function setGoogleMapHeight($v) {self::$GoogleMapHeight = $v; }
		public function getGoogleMapHeight() {return self::$GoogleMapHeight;}
	protected static $MapTypeDefaultZeroToTwo = 0;
		static function setMapTypeDefaultZeroToTwo($v) {self::$MapTypeDefaultZeroToTwo = $v;}
	protected static $ViewFinderSize = 100;
		static function setviewFinderSize($v) {self::$ViewFinderSize = $v;}
	protected static $MapAddTypeControl = false;
		static function setMapAddTypeControl($v) {self::$MapAddTypeControl = $v;}
	protected static $MapControlSizeOneToThree = 3;
		static function setMapControlSizeOneToThree($v) {self::$MapControlSizeOneToThree = $v;}
	protected static $MapScaleInfoSizeInPixels = 100;
		static function setMapScaleInfoSizeInPixels($v) {self::$MapScaleInfoSizeInPixels = $v;}
	protected static $DefaultLatitude = 0.000000001;
		static function setDefaultLatitude($v) {self::$DefaultLatitude = $v;}
	protected static $DefaultLongitude = 0.0000000001;
		static function setDefaultLongitude($v) {self::$DefaultLongitude = $v;}
	protected static $DefaultZoom = 0;
		static function setDefaultZoom($v) {self::$DefaultZoom = $v;}
	protected static $ShowStaticMapFirst = 0;
		static function setShowStaticMapFirst($v) {self::$ShowStaticMapFirst = $v;}
		public function getShowStaticMapFirst() {return self::$ShowStaticMapFirst;}
	/* STATIC MAP */
	protected static $StaticMapSettings = "maptype=roadmap";
		static function setStaticMapSettings($v) {self::$StaticMapSettings = $v;}
	protected static $StaticIcon = "red1";
		static function setStaticIcon($v) {self::$StaticIcon = $v;}
	protected static $LatFormFieldId = "";
		static function setLatFormFieldId($v) {self::$LatFormFieldId = $v;}
	protected static $LngFormFieldId = "";
		static function setLngFormFieldId($v) {self::$LngFormFieldId = $v;}

/* ADDRESS FINDER */
	protected static $AddAddressFinder = true;
		static function setAddAddressFinder($v) {self::$AddAddressFinder = $v;}
		public function getAddAddressFinder() {return self::$AddAddressFinder;}
	protected static $DefaultCountryCode = "NZ";
		static function setDefaultCountryCode($v) {self::$DefaultCountryCode = $v;}
	protected static $DefaultAddressText = " New Zealand";
		static function setDefaultAddressText($v) {self::$DefaultAddressText = $v;}

/* DIRECTIONS SETTINGS */
	protected static $StyleSheetUrl = true;
		static function setStyleSheetUrl($v) {self::$StyleSheetUrl = $v;}
	protected static $LocaleForResults = "en_NZ";
		static function setLocaleForResults($v) {self::$LocaleForResults = $v;}

/* JS SETTINGS */

/* DATA OBJECT */
	public $dataPointsXML;
	public $dataPointsObjectSet;
	public $dataPointsStaticMapHTML;

	protected $URLForData = Array();
	protected $GooglePointsDataObject = null;
	protected $dataObjectTitle = "";
	protected $whereStatementDescription = "";
	protected $fieldNameForGoogleDataObjectWithPages = "GoogleDataPoints";
	protected $Address = "";
	protected $filteredClassNameArray = Array();

	/* SERVER INTERACTION */
	protected $UpdateServerUrlAddressSearchPoint = "";
	protected $UpdateServerUrlDragend = "";

	/* Option 1 / 3 Set Address and update functions for Map */
	public function setAddress($v) {$this->Address = Convert::raw2js($v);}
	public function setUpdateServerUrlAddressSearchPoint($v) {$this->UpdateServerUrlAddressSearchPoint = Director::absoluteBaseURL().$v;}
	public function getUpdateServerURLAddressSearchPoint() {return $this->UpdateServerUrlAddressSearchPoint;}
	public function setUpdateServerUrlDragend($v) {$this->UpdateServerUrlDragend = Director::absoluteBaseURL().$v; }
	public function getUpdateServerUrlDragend() {return $this->UpdateServerUrlDragend;}
	public function allowAddPointsToMap() {self::$AddPointsToMap = true;}

	public function canEdit() {
		if($this->UpdateServerUrlDragend) {
			return true;
		}
	}

	public function orderItemsByLatitude($unsortedSet = null) {
		if(!$unsortedSet) {
			$unsortedSet = $this->dataPointsObjectSet;
		}
		$sortedSet = new DataObjectSet();
		if($unsortedSet->Count()) {
			foreach($unsortedSet as $item) {
				$tempArray[$item->Latitude] = $item;
			}
		}
		ksort($tempArray);
		foreach(array_reverse($tempArray) as $item) {
			$sortedSet->push($item);
		}
		return $sortedSet;
	}
	/* Option 2 / 3 Set Page DataObject for Map */
	public function setPageDataObjectSet($PageDataObjectSet) {
		if(count($PageDataObjectSet)) {
			$where = "ParentID IN (-1 ";
			foreach($PageDataObjectSet as $page) {
				if($page->HasGeoInfo) {
					$where .= ", ".$page->ID;
				}
			}
			$where .= ') ';
			$this->GooglePointsDataObject = DataObject::get("GoogleMapLocationsObject", $where);
			$PageDataObjectSet = null;
		}
	}

	/* Option 3 / 3 Set Points Directly Class = GoogleMapLocationsObject */
	public function setGooglePointsDataObject($GooglePointsDataObject) {
		$this->GooglePointsDataObject = $GooglePointsDataObject;
	}

	/* extra 1: set title */
	public function setDataObjectTitle($dataObjectTitle) {
		$this->dataObjectTitle = $dataObjectTitle;
	}
	/* extra 1: set where statement description */
	public function setWhereStatementDescription($whereStatementDescription) {
		$this->whereStatementDescription = $whereStatementDescription;
	}
	/* FILTER DATA e.g. Page, BusinessPage */
	public function setFilteredClassNameArray($array) {
		$this->filteredClassNameArray= $array;
	}
	/* Load Google Map into page  */
	public function loadGoogleMap($URLForData = "") {
		$js = '';
		$this->loadDefaults();
		$this->URLForData[] = $URLForData;
		ContentNegotiator::disable();
		if(!self::$includesDone) {
			Requirements::css("googleMap/css/map.css");
			//Requirements::javascript("mysite/javascript/jQuery.js");
			Requirements::javascript('googleMap/javascript/googleMapStatic.js');
			Requirements::javascript("googleMap/javascript/loadAjaxInfoWindow.js");
			Requirements::insertHeadTags('<style type="text/css">v\:* {behavior:url(#default#VML);}</style>');
			if(!self::$ShowStaticMapFirst || isset($_SESSION["staticMapsOff"]) && $_SESSION["staticMapsOff"]) {
				Requirements::javascript("http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".GoogleMapAPIKey);
				Requirements::javascript("googleMap/javascript/googleMaps.js");
				$js .= 'var scriptsLoaded = true; jQuery(document).ready( function() { initiateGoogleMap();} );';
			}
			else {
				$js .= 'var scriptsLoaded = false;';
				Requirements::javascript('http://www.google.com/jsapi?key='.GoogleMapAPIKey);
			}
			$js .= 'var absoluteBaseURL = "'. Director::absoluteBaseURL() .'";';
			$js .= $this->createJavascript();

			Requirements::customScript($js);
			self::$includesDone = true;
		}
	}


	public function getDataPointCount() {
		if($this->dataPointsObjectSet) {
			return $this->dataPointsObjectSet->Count();
		}
	}

	public function URLSegment() {
		$page = Director::currentPage();
		if($page) {
			return $page->URLSegment;
		}
	}

	/* turns 0 into false and 1 into true */
	private function showFalseOrTrue($v) {
		if($v) {
			return "true";
		}
		else{
			return "false";
		}
	}

 /* OUTPUT DATA */
	public function createDataPoints() {
		$this->dataPointsStaticMapHTML = '';
		$this->dataPointsXML = '';
		$this->dataPointsObjectSet = New DataObjectSet();
		$this->loadDefaults();
		if(self::$GoogleMapWidth > 512) { $staticMapWidth = 512;	}	else { $staticMapWidth = self::$GoogleMapWidth;	}
		if(self::$GoogleMapHeight > 512) { $staticMapHeight = 512;	}	else { $staticMapHeight = self::$GoogleMapHeight;	}
		$this->dataPointsStaticMapHTML = "size=".$staticMapWidth."x".$staticMapHeight;
		$totalCount = count($this->GooglePointsDataObject);
		if($totalCount > 0  && $totalCount < 500) {
			$count = 0;
			$pointsXml = '';
			$this->dataPointsStaticMapHTML .= '&amp;markers=';
			//the sort works, but Google Map does not seem to care...
			//$this->GooglePointsDataObject = $this->orderItemsByLatitude($this->GooglePointsDataObject);
			foreach($this->GooglePointsDataObject as $dataPoint) {
				$dataPoint->addParentData();
				if(!count($this->filteredClassNameArray) || in_array($dataPoint->ClassName, $this->filteredClassNameArray)) {
					if($dataPoint->PointType == "polygon") {
						$dataLine = '<Polygon><outerBoundaryIs><LinearRing><coordinates>'.$dataPoint->PointString.'</coordinates></LinearRing></outerBoundaryIs></Polygon>';
					}
					elseif($dataPoint->PointType == "polyline") {
						$dataLine = '<LineString><coordinates>'.$dataPoint->PointString.'</coordinates></LineString>';
					}
					else {
						$dataLine = '<Point><coordinates>'.$dataPoint->Longitude.','.$dataPoint->Latitude.'</coordinates></Point>';
					}
					$link = '';
					if($dataPoint->URLSegment) {
						$link = $dataPoint->AjaxInfoWindowLink;
					}
					if($dataPoint->staticIcon) {
						$staticIcon = $dataPoint->staticIcon;
					}
					else {
						$staticIcon = self::$StaticIcon;
					}
					if($count) {
					 $this->dataPointsStaticMapHTML .= '|';
					}
					$center = round($dataPoint->Latitude, 6).",".round($dataPoint->Longitude, 6);
					if(!$count) {
						$defaultCenter = $center;
					}
					$this->dataPointsStaticMapHTML .= $center.",".$staticIcon;
					$pointsXml .=
								'<Placemark>'.
								'<id>'.$dataPoint->ID.'</id>'.
								'<name>'.Convert::raw2xml($dataPoint->Name).'</name>'.
								$dataLine.
								'<description><![CDATA[ '.$dataPoint->AjaxInfoWindowLink.']]></description>'.
								'</Placemark>';
					$this->dataPointsObjectSet->push($dataPoint);
					$count++;
				}
			}
			if($count == 1) {
				$this->dataPointsStaticMapHTML .= '&amp;center='.$defaultCenter.'&amp;zoom='.self::$DefaultZoom;
			}
			$this->dataPointsXML =
						'<mapinfo>'.'<title>'.$this->dataObjectTitle.'</title>'
						.'<longitude>'.self::$DefaultLatitude.'</longitude>'
						.'<latitude>'.self::$DefaultLongitude.'</latitude>'
						.'<zoom>'.self::$DefaultZoom.'</zoom>'
						.'<pointcount>'.$count.'</pointcount>'
						.'<info>'.$this->whereStatementDescription.'</info>'
						.'</mapinfo>'
						.$pointsXml;
		}
		else {
			$this->dataPointsStaticMapHTML .=
				"&amp;center=".self::$DefaultLatitude.",".self::$DefaultLongitude.
				"&amp;zoom=".self::$DefaultZoom;
		}
		$this->dataPointsStaticMapHTML .= "&amp;".self::$StaticMapSettings;
		$this->dataPointsStaticMapHTML = '<img class="staticGoogleMap" src="http://maps.google.com/staticmap?'.$this->dataPointsStaticMapHTML.'&amp;key='.GoogleMapAPIKey.'" alt="map picture for '.$this->dataObjectTitle.'" />';
		return true;
	}

	/* OUTPUT JAVASCRIPT */
	private function createJavascript() {
		$js = '
		function loadSunnySideUpMap() {
		 if (GBrowserIsCompatible()) {
			GMO = new GMC("map", null,
			 {
		/* HELPDIVS */
				sideBarId:"'.self::$SideBarDivId.'",
				dropBoxId:"'.self::$DropDownDivId.'",
				titleId:"'.self::$TitleDivId.'",
				layerListId:"'.self::$LayerListDivId.'",
				directionsDivId:"'.self::$DirectionsDivId.'",
				statusDivId:"'.self::$StatusDivId.'",
				noStatusAtAll:'.$this->showFalseOrTrue(self::$NoStatusAtAll).',
				addKmlLink:'.$this->showFalseOrTrue(self::$AddKmlLink).',
				hiddenLayersRemovedFromList:'.$this->showFalseOrTrue(self::$HiddenLayersRemovedFromList).',
		/* PAGE*/
				changePageTitle:'.$this->showFalseOrTrue(self::$ChangePageTitle).',
				defaultTitle:"'.self::$DefaultTitle.'",
		/* INFOWINDOW*/
				infoWindowOptions:'.self::$InfoWindowOptions.',
				addAntipodean:'.$this->showFalseOrTrue(self::$AddAntipodean).',
				addDirections:'.$this->showFalseOrTrue(self::$AddDirections).',
				addCurrentAddressFinder:'.$this->showFalseOrTrue(self::$AddCurrentAddressFinder).',
		/* MARKER AND ICONS (include title to have a title)*/
				addPointsToMap:'.$this->showFalseOrTrue(self::$AddPointsToMap).',
				markerOptions: {bouncy:true,title: "click me", draggable: true},
				preloadImages:'.$this->showFalseOrTrue(self::$PreloadImages).',
				defaultIconUrl: "'.self::$DefaultIconUrl.'",
				iconFolder: "'.self::$IconFolder.'",
				iconWidth:'.self::$IconWidth.',
				iconHeight:'.self::$IconHeight.',
				iconExtension:"'.self::$IconExtension.'",
				iconMaxCount:'.self::$IconMaxCount.',
		/* POLYS */
				lineColour: "'.self::$LineColour.'",
				lineWidth: "'.self::$LineWidth.'",
				lineOpacity: "'.self::$LineOpacity.'",
				fillColour: "'.self::$FillColour.'",
				fillOpacity: "'.self::$FillOpacity.'",
				polyIcon: "'.self::$PolyIcon.'",
		/* MAP*/
				mapTypeDefaultZeroToTwo: '.intval(self::$MapTypeDefaultZeroToTwo+0).',
				viewFinderSize:'.intval(self::$ViewFinderSize + 0).',
				mapAddTypeControl:'.$this->showFalseOrTrue(self::$MapAddTypeControl).',
				mapControlSizeOneToThree:'.self::$MapControlSizeOneToThree.',
				mapScaleInfoSizeInPixels:'.intval(self::$MapScaleInfoSizeInPixels + 0).',
		/* START POSITION*/
				defaultLatitude:'.intval(self::$DefaultLatitude+0).',
				defaultLongitude:'.intval(self::$DefaultLongitude+0).',
				defaultZoom:'.intval(self::$DefaultZoom+0).',
		/* SERVER INTERACTION */
				updateServerUrlAddressSearchPoint: "'.$this->getUpdateServerUrlAddressSearchPoint().'",
				updateServerUrlDragend: "'.$this->getupdateServerUrlDragend().'",
				latFormFieldId:"'.self::$LatFormFieldId.'",
				lngFormFieldId:"'.self::$LngFormFieldId.'",
		/* ADDRESS FORM */
				addAddressFinder:'.$this->showFalseOrTrue(self::$AddAddressFinder).',
				defaultCountryCode:"'.self::$DefaultCountryCode.'",
				defaultAddressText:"'.self::$DefaultAddressText.'",
		/* DIRECTIONS */
				styleSheetUrl: "'.self::$StyleSheetUrl.'",
				localeForResults: "'.self::$LocaleForResults.'"
			 }
			);
		 }
		}
		function initiateGoogleMap() {
			if(!scriptsLoaded) {
				alert("load interactive map by clicking on it");
			}
			else {
				loadSunnySideUpMap();';
		if(count($this->URLForData)) {
			foreach($this->URLForData as $link) {
				$js .= '
				addLayer("'.Director::absoluteBaseURL().$link.'");';
			}
		}
		elseif($this->Address) {
			$js .= '
				findAddress(\''.$this->Address.'\')';
		}
		$js .= '
			}
		}';
		return $js;
	}

	private function loadDefaults() {
		if(!isset($this->whereStatementDescription)) {
			$this->whereStatementDescription = self::$WhereStatementDescription;
		}
		if(!isset($this->dataObjectTitle)) {
			$this->dataObjectTitle = self::$dataObjectTitle;
		}
	}

}


