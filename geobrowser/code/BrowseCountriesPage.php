<?php

class BrowseCountriesPage extends BrowseAbstractPage {

	static $icon = "geobrowser/images/treeicons/BrowseCountriesPage";
	static $allowed_children = array("BrowseRegionsPage");
	static $default_child = "BrowseRegionsPage";
	static $default_parent = "BrowseContinentsPage";
	static $can_be_root = false;
	//static $need_permission =";
	static $hide_ancestor = true;

	static $db = array(
		"Country" => "Varchar(50)" ,
		"ISO2" => "Varchar(2)" ,
		"Internet" => "Varchar(2)" ,
		"Capital" => "Varchar(25)" ,
		"NationalitySingular" => "Varchar(35)" ,
		"NationalityPlural" => "Varchar(35)" ,
		"Currency" => "Varchar(30)" ,
		"CurrencyCode" => "Varchar(3)" ,
		"Population" => "Int" ,
		"AdditionalTitle" => "Varchar(50)"
	);

	public static $breadcrumbs_delimiter = " &raquo; ";


	public function canCreate() {
		return DataObject::get_one(self::$default_parent);
	}

	public function GeoLevelName() {
		return "Countries";
	}

	public function GeoLevelNumber() {
		return 1;
	}

	public function allowBrowseChildren() {
		if( DataObject::get_one("BrowseWorldPage")->LevelOfDetail > $this->GeoLevelNumber() ) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$parents = DataObject::get("BrowseCountriesPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"] && $this->allowBrowseChildren()) {
			foreach($parents as $parent) {
				if($parent->HiddenDataID) {
					echo "<li>creating regions for ".$parent->Title."<ul>";
					$regions = $this->getDataFromTable("regions", "CountryID = ".$parent->HiddenDataID, "Region");
					foreach($regions as $region) {
						if(!DataObject::get("BrowseRegionsPage", "`BrowseAbstractPage`.`HiddenDataID` = ".$region["RegionID"])) {
							$page = new BrowseRegionsPage();
							$page->CreateRegion($region, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
				else {
					if(isset($_GET["geobuild"])) {debug::show("Parent does not exist");}
				}
			}
		}
	}

	public function CreateCountry(array $country, BrowseContinentsPage $parent) {
		if($parent && isset($country["Country"])) {
			$name = htmlentities($country["Country"]);
			if($name) {
				if(isset($_GET["geobuild"])) {echo "<li>creating ".$name."</li>";}
				$this->ParentID = $parent->ID;
				$this->Title = $name;
				$this->MetaTitle = $name;
				$this->PageTitle = $name;
				$this->HiddenDataID = $country["CountryID"];
				$this->ISO2 = $country["ISO2"];
				$this->Internet = $country["Internet"];
				$this->Capital = htmlentities($country["Capital"]);
				$this->NationalitySingular = htmlentities($country["NationalitySingular"]);
				$this->NationalityPlural = htmlentities($country["NationalityPlural"]);
				$this->Currency = $country["Currency"];
				$this->CurrencyCode = $country["CurrencyCode"];
				$this->Population = $country["Population"];
				$this->AdditionalTitle = htmlentities($country["Title"]);

				$this->CreateChildren = $parent->CreateAllChildren;
				$this->CreateAllChildren = $parent->CreateAllChildren;

				$this->writeToStage('Stage');
				$this->publish('Stage', 'Live');
			}
			else {
				if(isset($_GET["geobuild"])) {debug::show("name does not exist");}
			}
		}
		else {
			if(isset($_GET["geobuild"])) {debug::show("Parent does not exist");}
		}
	}
}

class BrowseCountriesPage_Controller extends BrowseAbstractPage_Controller {
	function init() {
		parent::init();
	}


}

