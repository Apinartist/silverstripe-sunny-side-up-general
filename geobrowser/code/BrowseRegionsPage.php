<?php

class BrowseRegionsPage extends BrowseAbstractPage {

	static $icon = "geobrowser/images/treeicons/BrowseRegionsPage";

	static $allowed_children = array("BrowseCitiesPage");

	static $default_child = "BrowseCitiesPage";

	static $default_parent = "BrowseCountriesPage";

	static $can_be_root = false;

	static $db = array(
		"Code" => "Varchar(8)",
	);

	public static $breadcrumbs_delimiter = " &raquo; ";

	public function canCreate() {
		return parent::canCreate();
	}

	public function GeoLevelName() {
		return "Regions";
	}

	public function GeoLevelNumber() {
		return 2;
	}

	public function allowBrowseChildren() {
		if(DataObject::get_one("BrowseWorldPage")->LevelOfDetail > $this->GeoLevelNumber()) {
			return true;
		}
		elseif("BrowseWorldPage" == $this->ClassName) {
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
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$parents = DataObject::get("BrowseRegionsPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"] && $this->allowBrowseChildren()) {
			foreach($parents as $parent) {
			  if($parent->CreateChildren && $parent->HiddenDataID) {
					echo "<li>creating cities for ".$parent->Title."<ul>";
					$cities = $this->getDataFromTable("cities", "RegionID = ".$parent->HiddenDataID, "City");
					foreach($cities as $city) {
						if(!DataObject::get_one("BrowseCitiesPage", "{$bt}BrowseAbstractPage{$bt}.{$bt}HiddenDataID{$bt} = ".$city["CityID"])) {
							$page = new BrowseCitiesPage();
							$page->CreateCity($city, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
			}
		  $parents->destroy();
		}
	}

	public function CreateRegion(array $region, BrowseCountriesPage $parent) {
		if($parent && isset($region["Region"])) {
			$name = htmlentities($region["Region"]);
			if($name) {
				if(isset($_GET["geobuild"])) {echo "<li>creating ".$name."</li>";}
				$this->ParentID = $parent->ID;
				$this->Title = $name;
				$this->MetaTitle = $name;
				$this->PageTitle = $name;
				$this->HiddenDataID = $region["RegionID"];

				$this->Code = $region["Code"];

				$this->CreateChildren = $parent->CreateAllChildren;
				$this->CreateAllChildren = $parent->CreateAllChildren;

				$this->URLSegment = $this->generateURLSegment($this->Title);

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

class BrowseRegionsPage_Controller extends BrowseAbstractPage_Controller {
	function init() {
		parent::init();
	}


}

