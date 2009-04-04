<?php
/**
 * Add a page to your site that allows you to view all the html that can be used in the typography section - if applied correctly.
 */


class TemplateOverviewPage extends Page {

	static $icon = "templateoverview/images/treeicons/TemplateOverviewPage";

	static $auto_include = false;

	static function setAutoInclude($value) {
		self::$auto_include = $value;
	}

	static $defaults = array(
		"URLSegment" => "templates",
		"ShowInMenus" => false,
		"ShowInSearch" => false,
		"Title" => "Templates overview",
	);

	public function canCreate() {
		return !DataObject::get("TemplateOverviewPage");
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

	var $TotalCount = 0;

	var $ShowAll = false;

	function init() {
		parent::init();
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript('templateoverview/javascript/TemplateOverviewPage.js');
		Requirements::themedCSS("templateoverview/css/TemplateOverviewPage.css");
	}

	function showall () {
		$this->ShowAll = true;
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
		$ListOfAllClasses =  new DataObjectSet();
		$classes = ClassInfo::subclassesFor("SiteTree");
		$j = 0;
		foreach($classes as $className) {
			if($className != "SiteTree" && $className != "TemplateOverviewPage") {
				if($this->ShowAll) {
					$objects = DataObject::get($className) {
						if(is_object($objects) && $objects->count()) {
							foreach($objects as $obj) {
								$indexNumber($obj->Sort);
								$object = $this->createPageObject($obj, $indexNumber);
								$ListOfAllClasses->push($object);
							}
						}
					}
				}
				else {
					$j++;
					$obj = null;
					$objects = DataObject::get($className, '', 'RAND()', '', 1);
					if(is_object($objects) && $objects->count()) {
						$obj = $objects->First();
						$count = DB::query("Select COUNT(*) from SiteTree_Live where ClassName = '$className'")->value();
					}
					else {
						$obj = singleton($className);
						$count = 0;
					}
					$indexNumber = (10000 * $count) + $j;
					$object = $this->createPageObject($obj, $indexNumber);
					$ListOfAllClasses->push($object);
				}
			}
		}
		$ListOfAllClasses->sort("indexNumber");
		$this->TotalCount = $ListOfAllClasses->count();
		return $ListOfAllClasses;
	}

	private function createPageObject($obj, $indexNumber) {
		$listArray = array();
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
		$staticIcon = $obj->stat("icon");
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

}

