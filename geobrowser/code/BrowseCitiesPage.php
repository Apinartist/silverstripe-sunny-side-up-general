<?php

class BrowseCitiesPage extends BrowseAbstractPage {

	static $icon = "geobrowser/images/treeicons/BrowseCitiesPage";

	static $default_parent = "BrowseRegionsPage";

	static $can_be_root = false;

	static $db = array(
		"Latitude" => "Double",
		"Longitude" => "Double",
		"TimeZone"=> "Varchar(10)",
		"County" => "Varchar(25)",
		"Code" => "Varchar(4)",
	);

	public function canCreate() {
		return parent::canCreate();
	}

	public static $breadcrumbs_delimiter = " &raquo; ";

	static function get_clostest_city_page($addressArray, $maxradius = 5000) {
		$existingDistance = $maxradius+1;
		$newDistance = $maxradius+1;
		$existingPage = null;
		$newPage = null;
		$radiusSelectionSQL = self::radiusDefinitionOtherTable($addressArray[0], $addressArray[1], "BrowseCitiesPage", "Latitude", "Longitude");
		$sqlQuery = new SQLQuery();
		$sqlQuery->select = array("`BrowseCitiesPage`.`ID`, ". $radiusSelectionSQL." as distance");
		$sqlQuery->from[] = "`BrowseCitiesPage`";
		$sqlQuery->where[] = $radiusSelectionSQL . " < ".$maxradius;
		$sqlQuery->orderby = " distance ";
		$sqlQuery->limit = "1";
		$result = $sqlQuery->execute();
		$page = null;
		foreach($result as $row) {
			$existingDistance = $row["distance"];
			$existingPage = DataObject::get_by_id("BrowseCitiesPage", $row["ID"]);
		}
		$radiusSelectionSQL = self::radiusDefinitionOtherTable($addressArray[0], $addressArray[1], "cities", "Latitude", "Longitude");
		$sqlQuery = new SQLQuery();
		$sqlQuery->select = array("cities.CityID", $radiusSelectionSQL." as distance");
		$sqlQuery->from[] = "`cities`";
		$sqlQuery->where[] = $radiusSelectionSQL . " < ".$maxradius;
		$sqlQuery->orderby = " distance ";
		$sqlQuery->limit = "1";
		$result = $sqlQuery->execute();
		foreach($result as $row) {
			$sameOne = false;
			if($existingPage) {
				if($row["CityID"] == $existingPage->HiddenDataID) {
					$sameOne = true;
				}
			}
			if(!$sameOne) {
				$newPage = self::create_city_and_parents($row["CityID"]);
				$newDistance = $row["distance"];
			}
		}
		if( ( $newPage) && ($newDistance < $existingDistance) &&  ($newDistance < $maxradius) ) {
			return $newPage;
		}
		elseif($existingPage && $existingDistance < $maxradius) {
			return $existingPage;
		}
		else {
			return false;
		}
	}

	static function radiusDefinitionOtherTable($lon, $lat, $table, $latitudeField, $longitudeField) {
		return '(6378.137 * ACOS( ( SIN( PI( ) * '.$lat.' /180 ) * SIN( PI( ) * `'.$table.'`.`'.$latitudeField.'` /180 ) ) + ( COS( PI( ) * '.$lat.' /180 ) * cos( PI( ) * `'.$table.'`.`'.$latitudeField.'` /180 ) * COS( (PI( ) * `'.$table.'`.`'.$longitudeField.'` /180 ) - ( PI( ) *'.$lon.' /180 ) ) ) ) ) ';
	}

	public function allowBrowseChildren() {
		return false;
	}


	public function GeoLevelName() {
		return "Cities";
	}

	public function GeoLevelNumber() {
		return 3;
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	static function create_city_and_parents($CityID) {
		//check if the city exists at all
		$sql = '
			SELECT cities.RegionID, regions.CountryID, countries.ContinentID From cities, regions, countries, continents
			WHERE
				cities.RegionID = regions.RegionID AND
				regions.CountryID = countries.CountryID AND
				countries.ContinentID = continents.ContinentID AND
				cities.CityID = '.$CityID.'
			LIMIT 1;';
		$result = DB::query($sql);
		foreach($result as $row) {
			print_r($row);
		}
		$abstractHelpPage = new BrowseAbstractPage();
		if($row) {
			//1 check if world exists
			if($worldPage = DataObject::get_one("BrowseWorldPage")) {
				//do nothing
			}
			else {
				$worldPage = new BrowseWorldPage();
				$name = "Find";
				$worldPage->Title = $name;
				$worldPage->MetaTitle = $name;
				$worldPage->PageTitle = $name;
				$worldPage->writeToStage('Stage');
				$worldPage->publish('Stage', 'Live');
			}

			//2 check if continent exists
			$ContinentID = $row["ContinentID"];
			if($continentPage = DataObject::get_one("BrowseContinentsPage", 'HiddenDataID = '.$ContinentID)) {
				//debug::show("continent exists");
			}
			else {
				//create continent
				$continents = $abstractHelpPage->getDataFromTable("continents", "ContinentID = ".$ContinentID, null);
				foreach($continents as $continentData) {
					$continentPage = new BrowseContinentsPage();
					$continentPage->CreateContinent($continentData, $worldPage);
				}
			}

			//3 check if country exists
			$CountryID = $row["CountryID"];
			if($countryPage = DataObject::get_one("BrowseCountriesPage", 'HiddenDataID = '.$CountryID)) {
				//debug::show("country exists");
			}
			else {
				//create Country
				$countries = $abstractHelpPage->getDataFromTable("countries", "CountryID = ".$CountryID, null);
				foreach($countries as $countryData) {
					$countryPage = new BrowseCountriesPage();
					$countryPage->CreateCountry($countryData, $continentPage);
				}
			}

			//4 check if region exists
			$RegionID = $row["RegionID"];
			if($regionPage = DataObject::get_one("BrowseRegionsPage", 'HiddenDataID = '.$RegionID)) {
				//debug::show("region exists");
			}
			else {
				//create region
				$regions = $abstractHelpPage->getDataFromTable("regions", "RegionID = ".$RegionID, null);
				foreach($regions as $regionData) {
					$regionPage = new BrowseRegionsPage();
					$regionPage->CreateRegion($regionData, $countryPage);
				}
			}
			if($cityPage = DataObject::get_one("BrowseCitiesPage", 'HiddenDataID = '.$CityID)) {
				//debug::show("city exists");
			}
			else {
				//create region
				$cities = $abstractHelpPage->getDataFromTable("cities", "CityID = ".$CityID, null);
				foreach($cities as $city) {
					$cityPage = new BrowseCitiesPage();
					$cityPage->CreateCity($city, $regionPage);
					return $cityPage;
				}
			}
		}
		return false;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(isset($_GET["urls"]) && isset($_GET["from"]) && isset($_GET["to"]) ) {
			$dos = DataObject::get("SiteTree", null, null, null, $_GET["from"].','.$_GET["to"]);
			foreach($dos as $page) {
				if(isset($page)) {
					echo "<li>fixing ".$page->Title."</li>";
					$page->URLSegment = $this->generateURLSegment($page->Title);
					$page->writeToStage('Stage');
					$page->publish('Stage', 'Live');
					$page->detroy();
				}
			}
		}
	}

	public function CreateCity(array $city, BrowseRegionsPage $parent) {
		if($parent && isset($city["City"])) {
			$name = htmlentities($city["City"]);
			if(isset($name)) {
				if(isset($_GET["geobuild"])) {echo "<li>creating ".$name."</li>";}
				$this->ParentID = $parent->ID;
				$this->Title = $name;
				$this->MetaTitle = $name;
				$this->PageTitle = $name;
				$this->HiddenDataID = $city["CityID"];

				$this->Code = $city["Code"];
				$this->Latitude = $city["Latitude"];
				$this->Longitude = $city["Longitude"];
				$this->TimeZone = $city["TimeZone"];
				$this->County = htmlentities($city["County"]);
				$this->Code = $city["Code"];

				$this->CreateChildren = $parent->CreateAllChildren;
				$this->CreateAllChildren = $parent->CreateAllChildren;

				$this->URLSegment = $parent->URLSegment."+".$this->generateURLSegment($this->Title);

				$this->writeToStage('Stage');
				$this->publish('Stage', 'Live');
			}
			else {
				if(isset($_GET["geobuild"])) {debug::show("No name can be found");}
			}
		}
		else {
			if(isset($_GET["geobuild"])) {debug::show("Parent does not exist");}
		}
	}



}

class BrowseCitiesPage_Controller extends BrowseAbstractPage_Controller {
	function init() {
		parent::init();
	}


}

