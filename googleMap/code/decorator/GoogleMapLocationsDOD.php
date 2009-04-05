<?php
/*
 *
 */


class GoogleMapLocationsDOD extends DataObjectDecorator {

	static $number_shown_in_around_me = 17;

	static $ajax_info_window_text = "View Details";

	function extraDBFields(){
		return array(
			'db' => array(
				"HasGeoInfo" => "Boolean",
			),
			'has_many' => array(
				"GeoPoints" => "GoogleMapLocationsObject"
			),
			'default' => array()
		);
	}

	protected $map = null;

	protected $isAjax = false;

	static function setNumberShownAroundMe($val) {
		self::$number_shown_in_around_me = $val - 0;
	}

	function augmentSQL(SQLQuery &$query) {	}

	function augmentDatabase() {}

	function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root", new Tab("Map"));
		$fields->addFieldToTab("Root.Map", new CheckboxField("HasGeoInfo", "Has Address(es)? - save and reload this page to start data-entry"));
		if($this->owner->HasGeoInfo) {
			$dataObject = new GoogleMapLocationsObject();
			$complexTableFields = $dataObject->complexTableFields();
			$popUpFields = $dataObject->getCMSFields_forPopup($this->owner->ID);
			$GeoPointsField = new ComplexTableField(
				$this->owner,
				'GeoPoints',
				'GoogleMapLocationsObject', //Classname
				$complexTableFields,
				$popUpFields,

				//'getCMSFields_forPopup',
				"ParentID = ".$this->owner->ID
			);
			$GeoPointsField->setParentClass($this->owner->class);
			//$GeoPointsField->setAddTitle( 'A Location' );
			$GeoPointsField->relationAutoSetting = true;
			$fields->addFieldToTab("Root.Map", $GeoPointsField);
		}
		return $fields;
 }

/* ******************************
 *  GENERAL FUNCTIONS
 *  ******************************
 */
	function GoogleMapController() {
		return $this->map;
	}

	public function hasMap() {
		if($this->map) {
			return true;
		}
		else {
			return false;
		}
	}

	public function AjaxInfoWindowLink() {
		return '<div class="viewMoreInformationHolder"><a href="'.$this->owner->URLSegment.'" onclick="return !loadAjaxInfoWindow(this,\''.$this->owner->URLSegment.'\');">'.self::$ajax_info_window_text.'</a><div class="loadAjaxInfoWindowSpan"></div></div>';
	}

	public function turnOffStaticMaps() {
		$_SESSION["staticMapsOff"] =  1;
	}

	protected function hasStaticMaps() {
  	return ((!isset($_SESSION["staticMapsOff"]) || !$_SESSION["staticMapsOff"]) && $this->map->getShowStaticMapFirst()) ? true : false;
	}

	static function hasStaticMapsStaticFunction() {
  	return ((!isset($_SESSION["staticMapsOff"]) || !$_SESSION["staticMapsOff"])  && $this->map->getShowStaticMapFirst()) ? true : false;
	}

	private function initiateMap() {
		if(!$this->map) {
			$this->map = new GoogleMap();
		}
	}

/* ******************************
 * CREATING MAPS ON PAGES - SEVERAL OPTIONS
 *  - addEmptyMap,  $UpdateServerUrlAddPoint = ""
 *  - addAddressMap
 *  - PagePointsMap (current page points):  use addMap with "showPagePointsMapXML"
 *  - ChildPointsMap (current children points): use addMap with "showChildPointsMapXML"
 *  ******************************
 */

	function addMap($action = "") {
		$this->initiateMap();
		if($action) {
			$URLForData = $this->owner->URLSegment."/".$action;
		}
		else {
			$URLForData  = '';
		}
		if(!strpos($URLForData,'?')!== false) {
			$URLForData .= '?';
		}
		$URLForData .= '&getXML=1';
		$this->map->loadGoogleMap($URLForData);
		if(!Director::is_ajax()) {
			if($this->hasStaticMaps()) {
				if(method_exists($this, $action)) {
					$this->$action();
				}
			}
		}
		return Array();
	}

	function addAddress($address = '') {
		$this->initiateMap();
		if(!$address && isset($_REQUEST["address"])) {
			$address = urlencode($_REQUEST["address"]);
		}
		if($address) {
			$this->map->setAddress($address);
		}
		else {
			user_error("No address could be added.", E_USER_ERROR);
		}
	}

	function addUpdateServerUrlAddressSearchPoint($UpdateServerUrlAddPoint = "showAroundMeXML/?getXML=1") {
		$this->initiateMap();
		$v = $this->owner->URLSegment."/".$UpdateServerUrlAddPoint;
		die($v);
		$this->map->setUpdateServerUrlAddressSearchPoint($v);
	}

	function addUpdateServerUrlDragend($UpdateServerUrlDragend = "updateMeXML/?getXML=1") {
		$this->initiateMap();
		$v = $this->owner->URLSegment."/".$UpdateServerUrlDragend;
		$this->map->setUpdateServerUrlDragend($v);
	}

	function addAllowAddingAndDeletingPoints() {
		$this->initiateMap();
		$this->map->allowAddPointsToMap();
	}

	function addCustomMap($pages, $retainOldSessionData = false) {
		if($pages) {
			//Session::clear("addCustomGoogleMap");
			if(!$retainOldSessionData) {
				$_SESSION["addCustomGoogleMap"] =  null;
				$_SESSION["addCustomGoogleMap"] =  array();
			}
			foreach($pages as $page) {
				if(!$page->ID) {
					user_error("Page provided to addCustomMap that does not have an ID", E_USER_ERROR);
				}
				$_SESSION["addCustomGoogleMap"][] = $page->ID;
			}
		}
		Session::save();
		$this->addMap("showCustomMapXML", null);
		return Array();
	}

/* ******************************
 * RETURNING DATA FOR MAPS USING AJAX
 * ******************************
 */

	public function turnOnStaticMaps() {
		$_SESSION["staticMapsOff"] =  null;
		unset($_SESSION["staticMapsOff"]);
		return "static maps will be loaded first";
	}

	public function showPagePointsMapXML() {
		$data = DataObject::get("GoogleMapLocationsObject", "ParentID = ".$this->owner->ID);
		if($data) {
			return $this->makeXMLData(null, $data, "Locations for ".$this->owner->Title, "Locations for ".$this->owner->Title);
		}
	}

	public function showChildPointsMapXML() {
		if($children = $this->getChildrenOfType($this->owner, null)) {
			return $this->makeXMLData($children, null, "Points related to ".$this->owner->Title, "Points related to ".$this->owner->Title);
		}
	}

	public function showEmptyMap() {
		return $this->makeXMLData(null, null, "Points related to ".$this->owner->Title, "Points related to ".$this->owner->Title);
	}

	public function showCustomMapXML() {
		$array = Array();
		if(isset($_SESSION["addCustomGoogleMap"])) {
			$array = $_SESSION["addCustomGoogleMap"];
		}
		//print_r($array);
		$where = " `SiteTree`.`ID` IN (-1 ";
		if(is_array($array)) {
			foreach($array as $id) {
				if($id > 0) {
					$where .= ", ".$id;
				}
			}
		}
		$where .= ")";
		$pages = DataObject::get("SiteTree",$where);
		return $this->makeXMLData($pages, null, "Search results", "Search results");
	}

	public function showAroundMeXML() {
		if(isset($_REQUEST["x"]) && isset($_REQUEST["y"]) ) {
			$lon = floatval($_REQUEST["x"]);
			$lat = floatval($_REQUEST["y"]);
			$orderByRadius = GoogleMapLocationsObject::radiusDefinition($lon, $lat);
			$where = "(".$orderByRadius.") > 0 AND Latitude <> 0 AND Longitude <> 0";
			$objects = DataObject::get("GoogleMapLocationsObject", $where, $orderByRadius, '', self::$number_shown_in_around_me );
			if(is_object($objects)) {
				return $this->makeXMLData(null, $objects, "Closest to me", self::$number_shown_in_around_me . " points closest ones");
			}
			else {
				//return false;
			}
		}
	}


	public function updateMeXML() {
		if($this->owner->canEdit()) {
			if(isset($_REQUEST["x"]) && isset($_REQUEST["y"]) && isset($_REQUEST["i"]) ) {
				$lon = floatval($_REQUEST["x"]);
				$lat = floatval($_REQUEST["y"]);
				$id = intval($_REQUEST["i"]);
				if($lon && $lat) {
					if( 0 == $id ) {
						$point = new GoogleMapLocationsObject;
						$point->ParentID = $this->owner->ID;
						$point->Latitude = $lat;
						$point->Longitude = $lon;
						$point->write();
						return $point->ID;
					}
					elseif($id > 0) {
						$point = DataObject::get_by_id("GoogleMapLocationsObject", $id);
						if($point) {
							if($point->ParentID == $this->owner->ID) {
								$point->Latitude = $lat;
								$point->Longitude = $lon;
								$point->Address = "";
								$point->FullAddress = "";
								$point->write();
								return  "location updated";
							}
							else {
								return "you dont have permission to update that location";
							}
						}
						else {
							return "could not find location";
						}
					}
					elseif($id < 1) {

						$point = DataObject::get_by_id("GoogleMapLocationsObject", (-1 * $id));
						if($point) {
							if($point->ParentID == $this->owner->ID) {
								$point->delete();
								$point = null;
								return "location deleted";
							}
							else {
								return "you dont have permission to delete that location";
							}
						}
						else {
							return "could not find location";
						}
					}
				}
			}
		}
		return  "point could NOT be updated.";
	}


/* ******************************
 * PRIVATE PARTY BELOW
 * ******************************
 */


	private function makeXMLData($PageDataObjectSet = null, $GooglePointsDataObject = null, $dataObjectTitle = '', $whereStatementDescription = '') {
		if(!$this->hasMap()) {
			$this->map = new GoogleMap();
		}
		$this->map->setDataObjectTitle($dataObjectTitle);
		$this->map->setWhereStatementDescription($whereStatementDescription);
		if($GooglePointsDataObject) {
			$this->map->setGooglePointsDataObject($GooglePointsDataObject);
		}
		elseif($PageDataObjectSet) {
			$this->map->setPageDataObjectSet($PageDataObjectSet);
		}
		else {
			$this->staticMapHTML = "<p>No points found</p>";
		}
		$data = $this->map->createDataPoints();
		if(Director::is_ajax() || (isset($_GET["getXML"]) && $_GET["getXML"])) {
			//$this->dataPointsXML = $data[1];
			$this->turnOffStaticMaps();
			return $this->owner->renderWith("GoogleMapXml");
		}
		else {
			//$this->dataMapObjectSet = $data[0];
			//$this->staticMapHTML = $data[2];
		}
	}

	/**
	 * Recursively search children of current page to find a particular classtype
	 *
	 * @param $obj DataObject The Object of which you want to find the children
	 * @param $classType String The text string to match `ClassName` field
	 * @return DataObjectSet of Class $classType
	 */
	private function getChildrenOfType($CurrentPage,$classType=null) {
		$children = $CurrentPage->AllChildren();
		if (!isset($childrenOfType)) $childrenOfType=new DataObjectSet();
		if ($children) {
			foreach($children as $item ) {
				$childrenOfType->merge($this->getChildrenOfType($item,$classType));
			}
		}
		if((isset($classType) && $CurrentPage->ClassName == $classType) || (!isset($classType))) {
			if($CurrentPage->HasGeoInfo) {
				$childrenOfType->push($CurrentPage);
			}
		}
		return ($childrenOfType) ? $childrenOfType : new DataObjectSet();
	}
}

class GoogleMapLocationsDOD_Controller extends DataObjectDecorator {



}
