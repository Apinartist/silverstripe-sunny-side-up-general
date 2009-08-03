<?php
/**
 * Add a page to your site that allows you to view all the html that can be used in the typography section - if applied correctly.
 */


class TemplateOverviewPage extends Page {

	static $icon = "templateoverview/images/treeicons/TemplateOverviewPage";

	static $auto_include = false;

	static function set_auto_include($value) {
		self::$auto_include = $value;
	}

	static $defaults = array(
		"URLSegment" => "templates",
		"ShowInMenus" => false,
		"ShowInSearch" => false,
		"Title" => "Template overview (internal use only)",
		"MenuTitle" => "Template overview",
	);

	static $has_many = array(
		"TemplateOverviewDescriptions" => "TemplateOverviewDescription"
	);

	public function canCreate() {
		return !DataObject::get("SiteTree", 'ClassName = "TemplateOverviewPage"');
	}

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
		$fields->addFieldToTab('Root.Content.Advertisements', $tablefield);
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
				$page->ShowInSearch = 0;
				$page->Title = "Templates overview";
				$page->MetaTitle = "Templates overview";
				$page->PageTitle = "Templates overview";
				$page->Sort = 99998;
				$page->URLSegment = "templates";
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				Database::alteration_message("TemplateOverviewPage","created");
			}
		}
	}

}

class TemplateOverviewPage_Controller extends Page_Controller {

	var $counter = 0;

	var $ShowAll = false;

	function init() {
		parent::init();
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript('templateoverview/javascript/TemplateOverviewPage.js');
		Requirements::css("templateoverview/css/TemplateOverviewPage.css");
	}

	function showall () {
		$this->ShowAll = true;
		return array();
	}

	function showmore() {
		$id = Director::URLParam("ID");
		$obj = DataObject::get_by_id("SiteTree", $id);
		if($obj) {
			$data = DataObject::get($obj->ClassName);
		}
		$array = array("Results" => $data);
		return $this->customise($array)->renderWith("TemplateOverviewPageShowMoreList");
	}

	public function ListOfAllClasses() {
		$ArrayOfAllClasses =  Array();
		$classes = ClassInfo::subclassesFor("SiteTree");
		foreach($classes as $className) {
			if($className != "SiteTree" && $className != "TemplateOverviewPage" ) {
				if($this->ShowAll) {
					$objects = DataObject::get($className, 'ClassName = "'.$className.'"');
					$count = 0;
					if(is_object($objects) && $objects->count()) {
						foreach($objects as $obj) {
							$object = $this->createPageObject($obj, $count++, $className);
							$obj->TemplateOverviewDescription = $this->getTemplateOverviewDescription($className);
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

	public function getTemplateOverviewDescription($className) {
		$obj = DataObject::get_one("TemplateOverviewDescription", 'ClassNameLink = "'.$className.'"');
		if(!$obj) {
			$obj = new TemplateOverviewDescription();
			$obj->ClassNameLink = $className;
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
		if($count > 1) {
			$listArray["AddShowMoreLink"] = true;
		}
		else {
			$listArray["AddShowMoreLink"] = false;
		}
		$listArray["ID"] = $obj->ID;
		$listArray["TypoURLSegment"] = $this->URLSegment;
		$listArray["Title"] = $obj->Title;
		$listArray["FullLink"] = Director::absoluteBaseURL().$obj->URLSegment;
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

	function FinalClass($obj) {
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

