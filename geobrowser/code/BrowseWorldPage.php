<?php



class BrowseWorldPage extends BrowseAbstractPage {

	static $icon = "geobrowser/images/treeicons/BrowseWorldPage";

	static $allowed_children = array("BrowseContinentsPage");

	static $default_child = "BrowseContinentsPage";

	static $db = array(
		"LevelOfDetail" => "Int",
	);

	public static $breadcrumbs_delimiter = " &raquo; ";

	static $defaults = array(
		"CreateChildren" => true,
		"LevelOfDetail" => 2
	);

	static $LevelOfDetailArray = Array (
		"0" => "Continents",
		"1" => "Countries",
		"2" => "Regions",
		"3" => "Cities"
	);

	public function canCreate() {
		return parent::canCreate();
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.AddSubRegion", new DropdownField("LevelOfDetail", "Greatest Level of Detail", self::$LevelOfDetailArray));
		return $fields;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$parents = DataObject::get("BrowseWorldPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"]) {
			foreach($parents as $parent) {
				if($parent->CreateChildren) {
					echo "<li>creating continents for ".$parent->Title."<ul>";
					$continents = $this->getDataFromTable("continents", null, "Continent");
					foreach($continents as $continent) {
						if(!DataObject::get("BrowseContinentsPage", "`BrowseAbstractPage`.`HiddenDataID` = ".$continent["ContinentID"])) {
							$page = new BrowseContinentsPage();
							$page->CreateContinent($continent, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
			}
		}
	}
}

class BrowseWorldPage_Controller extends BrowseAbstractPage_Controller {
	function init() {
		parent::init();
	}


}

