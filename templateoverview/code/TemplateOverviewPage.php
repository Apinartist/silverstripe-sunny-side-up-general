<?php
/**
 *@author: nicolaas [at] sunnysideup.co.nz
 *@description Add a page to your site that allows you to view all the html that can be used in the typography section - if applied correctly.
 */


class TemplateOverviewPage extends Page {

	static $icon = "templateoverview/images/treeicons/TemplateOverviewPage";

	protected static $auto_include = false;
		static function set_auto_include($value) {self::$auto_include = $value;}
		static function get_auto_include() {return self::$auto_include;}

	protected static $parent_url_segment = "admin-only";
		static function set_parent_url_segment($value) {self::$parent_url_segment = $value;}
		static function get_parent_url_segment() {return self::$parent_url_segment;}

	protected static $classes_to_exclude = array("SiteTree", "TemplateOverviewPage", "RedirectorPage", "VirtualPage", "GhostPage") {
		static function set_classes_to_exclude($array) {self::$classes_to_exclude = $array;}
		static function get_classes_to_exclude() {return self::$classes_to_exclude;}


	static $defaults = array(
		"URLSegment" => "templates",
		"ShowInMenus" => 0,
		"ShowInSearch" => 0,
		"Title" => "Template overview (internal use only)",
		"MenuTitle" => "Template overview",
	);

	static $has_many = array(
		"TemplateOverviewDescriptions" => "TemplateOverviewDescription"
	);

	public function canCreate() {
		return !DataObject::get("SiteTree", "`ClassName` = 'TemplateOverviewPage'");
	}


	protected $counter = 0;

	protected $ShowAll = false;

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$tablefield = new HasManyComplexTableField(
			$controller = $this,
			$name = 'TemplateOverviewDescriptions',
			$sourceClass = 'TemplateOverviewDescription',
			null,
			$detailFormFields = 'getCMSFields_forPopup',
			$sourceFilter = "`ParentID` = ".$this->ID,
			$sourceSort = "`ClassNameLink` ASC"
			//$sourceJoin = null
		);
		$fields->addFieldToTab('Root.Content.Descriptions', $tablefield);
		return $fields;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(self::$auto_include) {
			$check = DataObject::get_one("TemplateOverviewPage");
			if(!$check) {
				$page = new TemplateOverviewPage();
				$page->ShowInMenus = 0;
				$page->ShowInSearch = 0;
				$page->Title = "Templates overview";
				$page->MetaTitle = "Templates overview";
				$page->PageTitle = "Templates overview";
				$page->Sort = 99998;
				$page->URLSegment = "templates";
				$parent = DataObject::get_one("Page", "URLSegment = '".self::$parent_url_segment."'");
				if($parent) {
					$page->ParentID = $parent->ID;
				}
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				$page->URLSegment = "templates";
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				Database::alteration_message("TemplateOverviewPage","created");
			}
		}
	}


	public function ListOfAllClasses() {
		$ArrayOfAllClasses =  Array();
		$classes = ClassInfo::subclassesFor("SiteTree");
		foreach($classes as $className) {
			if(!in_array($className, self::$classes_to_exclude))) {
				if($this->ShowAll) {
					$objects = DataObject::get($className, 'ClassName = "'.$className.'"');
					$count = 0;
					if(is_object($objects) && $objects->count()) {
						foreach($objects as $obj) {
							$object = $this->createPageObject($obj, $count++, $className);
							$ArrayOfAllClasses[$object->indexNumber] = clone $object;
						}
					}
				}
				else {
					$obj = null;
					$objects = DataObject::get($className, 'ClassName = "'.$className.'"', 'RAND() ASC', '', 1);
					if(is_object($objects) && $objects->count()) {
						$obj = $objects->First();
						$count = DB::query('Select COUNT(*) from SiteTree_Live where ClassName = "'.$obj->ClassName.'"')->value();
					}
					else {
						$obj = singleton($className);
						$count = 0;
					}
					$object = $this->createPageObject($obj, $count, $className);
					$object->TemplateOverviewDescription = $this->TemplateDetails($className);
					$ArrayOfAllClasses[$object->indexNumber] = clone $object;
				}
			}
		}
		ksort($ArrayOfAllClasses);
		$doSet =  new DataObjectSet();
		if(count($ArrayOfAllClasses)) {
			foreach($ArrayOfAllClasses as $item) {
				$doSet->push($item);
			}
		}
		return $doSet;
	}

	function showall () {
		$this->ShowAll = true;
		return array();
	}


	protected function TemplateDetails($className) {
		$obj = DataObject::get_one("TemplateOverviewDescription", 'ClassNameLink = "'.$className.'" AND ParentID = '.$this->ID);
		if(!$obj) {
			$obj = new TemplateOverviewDescription();
			$obj->ClassNameLink = $className;
			$obj->ParentID = $this->ID;
			$obj->write();
		}
		return $obj;
	}

	public function TotalCount () {
		return count(ClassInfo::subclassesFor("SiteTree"))-1;
	}

	private function createPageObject($obj, $count, $className) {
		$this->counter++;
		$listArray = array();
		$indexNumber = (10000 * $count) + $this->counter;
		$listArray["indexNumber"] = $indexNumber;
		$listArray["ClassName"] = $className;
		$listArray["Count"] = $count;
		$listArray["ID"] = $obj->ID;
		$listArray["TypoURLSegment"] = $this->URLSegment;
		$listArray["Title"] = $obj->Title;
		$listArray["FullLink"] = Director::absoluteURL($obj->Link());
		$listArray["URLSegment"] = $obj->URLSegment;
		$staticIcon = $obj->stat("icon", true);
		if(is_array($staticIcon)) {
			$iconArray = $obj->stat("icon");
			$icon = $iconArray[0];
		}
		else {
			$icon = $obj->stat("icon");
		}
		$listArray["Icon"] = $icon."-file.gif";
		$object = new ArrayData($listArray);
		return $object;
	}

	//not used!
	function NoSubClasses($obj) {
		$array = ClassInfo::subclassesFor($obj->ClassName);
		if(count($array)) {
			foreach($array as $class) {
				if(DataObject::get_by_id($class, $obj->ID)) {
					return false;
				}
			}
		}
		return true;
	}

}

class TemplateOverviewPage_Controller extends Page_Controller {


	function init() {
		parent::init();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript('templateoverview/javascript/TemplateOverviewPage.js');
		Requirements::css("templateoverview/css/TemplateOverviewPage.css");
		if(class_exists("PrettyPhoto")) {
			PrettyPhoto::include_code();
		}
		else {
			user_error("It is recommended that you install the Sunny Side Up Pretty Photo Module", E_USER_NOTICE);
		}
	}

	function showmore() {
		$id = Director::URLParam("ID");
		$obj = DataObject::get_by_id("SiteTree", $id);
		if($obj) {
			$data = DataObject::get($obj->ClassName, $where = "`ClassName` = '".$obj->ClassName."'", $orderBy = "", $join = "", $limit = 500);
		}
		$array = array(
			"Results" => $data,
			"MoreDetail" => DataObject::get("TemplateOverviewDescription", "ClassNameLink = '".$obj->ClassName."'")
		);
		return $this->customise($array)->renderWith("TemplateOverviewPageShowMoreList");
	}




	function ConfigurationDetails() {
		$m = Member::CurrentMember();
		if($m) {
			if($m->IsAdmin()) {
				$baseFolder = Director::baseFolder();
				$myFile = $baseFolder."/".$this->project()."/_config.php";
				$fh = fopen($myFile, 'r');
				$string = '';
				while(!feof($fh)) {
					$string .= fgets($fh, 1024);
				}
				fclose($fh);
				return $string;
			}
		}

	}

}

