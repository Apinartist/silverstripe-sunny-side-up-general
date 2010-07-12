<?php

class BrowseContinentsPage extends BrowseAbstractPage {

	static $icon = "geobrowser/images/treeicons/BrowseContinentsPage";

	static $allowed_children = array("BrowseCountriesPage");

	static $default_child = "BrowseCountriesPage";

	static $default_parent = "BrowseWorldPage";

	static $db = array(
		"ExtraNote" => "Varchar(255)"
	);

	public static $breadcrumbs_delimiter = " &raquo; ";

	public function canCreate() {
		return parent::canCreate();
	}

	public function GeoLevelName() {
		return "Continents";
	}

	public function GeoLevelNumber() {
		return 0;
	}



	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	public function allowBrowseChildren() {
		if ( DataObject::get_one("BrowseWorldPage")->LevelOfDetail > $this->GeoLevelNumber() ) {
			return true;
		}
		else {
			return false;
		}
	}

	public function requireDefaultRecords() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		parent::requireDefaultRecords();
		$parents = DataObject::get("BrowseContinentsPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"] && $this->allowBrowseChildren()) {
			foreach($parents as $parent) {
				if($parent->CreateChildren && $parent->HiddenDataID) {
					echo "<li>creating countries for ".$parent->Title."<ul>";
					$countries = $this->getDataFromTable("countries", "ContinentID = ".$parent->HiddenDataID, "Country");
					foreach($countries as $country) {
						if(!DataObject::get_one("BrowseCountriesPage", "{$bt}HiddenDataID{$bt} = ".$country["CountryID"])) {
							$page = new BrowseCountriesPage();
							$page->CreateCountry($country, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
			}
		}
	}
	public function CreateContinent(array $continent, BrowseWorldPage $parent) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($parent && isset($continent["Continent"])) {
			$name = htmlentities($continent["Continent"]);
			if($name) {
				if(isset($_GET["geobuild"])) {echo "<li>creating ".$name."</li>";}
				$this->ParentID = $parent->ID;
				$this->Title = $name;
				$this->MetaTitle = $name;
				$this->PageTitle = $name;
				$this->HiddenDataID = $continent["ContinentID"];
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

class BrowseContinentsPage_Controller extends BrowseAbstractPage_Controller {
	function init() {
		parent::init();
	}


}

