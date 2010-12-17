<?php
/*
 *
 */


class GoogleMapLocationsDOD extends DataObjectDecorator {

	protected static $page_classes_without_map = array();
		static function get_page_classes_without_map(){return self::$page_classes_without_map;}
		static function set_page_classes_without_map(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in GoogleMapLocationsDOD::set_page_classes_without_map()");}
			self::$page_classes_without_map = $array;
		}

	protected static $page_classes_with_map = array();
		static function get_page_classes_with_map(){return self::$page_classes_with_map;}
		static function set_page_classes_with_map(array $array){
			if(!is_array($array)) {debug::show("argument needs to be an array in GoogleMapLocationsDOD::set_page_classes_with_map()");}
			self::$page_classes_with_map = $array;
		}


	function extraStatics(){
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
		user_error("setNumberShownAroundMe has been deprecated - use GoogleMap::set_number_shown_in_around_me instead", E_USER_NOTICE);
		self::$number_shown_in_around_me = $val - 0;
	}

	function augmentSQL(SQLQuery &$query) {	}

	function augmentDatabase() {}

	function updateCMSFields(FieldSet &$fields) {
		if($this->classHasMap()) {
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
		}
		return $fields;
 }

/* ******************************
 *  GENERAL FUNCTIONS
 *  ******************************
 */
	function GoogleMapController() {
		$this->map->loadGoogleMap();
		return $this->map;
	}

	public function hasMap() {
		if($this->map && $this->classHasMap()) {
			return true;
		}
		else {
			return false;
		}
	}

	public function AjaxInfoWindowLink() {
		if($this->owner->hasMethod("CustomAjaxInfoWindow")) {
			return $this->owner->CustomAjaxInfoWindow();
		}
		if($this->owner->hasMethod("ajaxinfowindowreturn")) {
			return '<div class="viewMoreInformationHolder"><a href="'.$this->owner->Link().'" onclick="return !loadAjaxInfoWindow(this,\''.$this->owner->Link().'ajaxinfowindowreturn/\');">'.GoogleMap::get_ajax_info_window_text().'</a><div class="loadAjaxInfoWindowSpan"></div></div>';
		}
	}

	protected function hasStaticMaps() {
  	return (!Session::get("StaticMapsOff") && $this->map->getShowStaticMapFirst()) ? true : false;
	}


	static function hasStaticMapsStaticFunction() {
  	return (!Session::get("StaticMapsOff") && $this->map->getShowStaticMapFirst()) ? true : false;
	}

	private function initiateMap() {
		if(!$this->map) {
			$this->map = new GoogleMap();
		}
	}

	function addMap($action = "", $title = "", $lng = 0, $lat = 0, $filter = "") {
		$this->initiateMap();
		if(!$title) {
			$title = $this->owner->Title;
		}
		$linkForData = "googlemap/".$action."/".$this->owner->ID."/".urlencode($title)."/".$lng."/".$lat."/";
		if($filter) {
			$linkForData .= "/".urlencode($filter)."/";
		}
		$this->map->addLayer($linkForData);
		if(!Director::is_ajax()) {
			if($this->hasStaticMaps()) {
				$controller = new GoogleMapDataResponse();
				if(method_exists($controller, $action)) {
					$controller->setOwner($this->owner);
					$controller->setTitle($title);
					$controller->setLng($lng);
					$controller->setLat($lat);
					$controller->setFilter($filter);
					return $controller->$action();
				}
			}
		}
		return Array();
	}

	public function addExtraLayersAsAction($action = "", $title = "", $lng = 0, $lat = 0, $filter = "") {
		$linkForData = "googlemap/".$action."/".$this->owner->ID."/".urlencode($title)."/".$lng."/".$lat."/";
		if($filter) {
			$linkForData .= "/".urlencode($filter)."/";
		}
		$this->addExtraLayersAsLinks($title, $linkForData);
	}

	public function addExtraLayersAsLinks($title, $link) {
		$this->map->addExtraLayersAsLinks($title, $link);
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

	function addUpdateServerUrlAddressSearchPoint($UpdateServerUrlAddPoint = "/googlemap/showaroundmexml/") {
		$this->initiateMap();
		$this->map->setUpdateServerUrlAddressSearchPoint($UpdateServerUrlAddPoint);
	}

	function addUpdateServerUrlDragend($UpdateServerUrlDragend = "/googlemap/updatemexml/") {
		$this->initiateMap();
		$this->map->setUpdateServerUrlDragend($UpdateServerUrlDragend);
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
		$this->addMap("showcustommapxml");
		return Array();
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


	protected function classHasMap() {
		//assumptions:
		//1. in general YES
		//2. if list of WITH is shown then it must be in that
		//3. otherwise check if it is specifically excluded (WITHOUT)
		$result = true;
		$inc =  self::get_page_classes_with_map();
		$exc =  self::get_page_classes_without_map();
		if(is_array($inc) && count($inc)) {
			$result = false;
			if(in_array($this->owner->ClassName,$inc)) {
				$result = true;
			}
		}
		elseif(is_array($exc) && count($exc) && in_array($this->owner->ClassName,$exc))  {
			$result = false;
		}
		return $result;
	}

}

class GoogleMapLocationsDOD_Controller extends Extension {

	static $allowed_actions = array("SearchByAddressForm");

	var $address = false;

	function SearchByAddressForm() {
		return new Form(
			$this->owner,
			"SearchByAddressForm",
			new FieldSet(new TextField("Address", _t("GoogleMapLocationsDOD.ENTERADDRESS", "Enter your address"),$this->address)),
			new FieldSet(new FormAction("findnearaddress", _t("GoogleMapLocationsDOD.SEARCH", "Search"))),
			new RequiredFields("Address")
		);
	}

	function findnearaddress($data, $form) {
		$address = Convert::raw2sql($data["Address"]);
		GoogleMapSearchRecord::create_new(Convert::raw2sql($address));
		$pointArray = GetLatLngFromGoogleUsingAddress::get_placemark_as_array($address);
		$this->address = $pointArray["address"];
		if(!isset($pointArray[0]) || !isset($pointArray[0])) {
			$form->addErrorMessage('Address', _t("GoogleMapLocationsDOD.ADDRESSNOTFOUND", "Sorry, address could not be found..."), 'warning');
			Director::redirectBack();
			return;
		}
		$lng = $pointArray[0];
		$lat = $pointArray[1];
		//$form->Fields()->fieldByName("Address")->setValue($pointArray["address"]); //does not work ....
		$this->owner->addMap($action = "showsearchpoint", "Your search",$lng, $lat);
		$this->owner->addMap($action = "showaroundmexml","Closests to your search", $lng, $lat);
		return array();
	}

}



