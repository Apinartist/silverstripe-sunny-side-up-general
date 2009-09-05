<?php

class BrowseAbstractPage extends Page {

	static $db = array(
		"CreateChildren" => "Boolean",
		"CreateAllChildren" => "Boolean",
		"HiddenDataID" => "Int",
		"AlternativeURL" => "Varchar"
	);

	public static $breadcrumbs_delimiter = " &raquo; ";

	public function canCreate() {
		if("BrowseAbstractPage" == $this->ClassName) {
			return false;
		}
		else {
			return DataObject::get_one("SiteTree", '`ClassName` = "'.self::$default_parent.'"');
		}
	}

	public function GeoLevelName() {
		return "No level";
	}

	public function GeoLevelNumber() {
		return -1;
	}


	public function allowBrowseChildren() {
		return true;
	}

	protected function getDataFromTable($tableName, $where  = null, $orderby = null) {
		$sqlQuery = new SQLQuery();
		$sqlQuery->select = array('*');
		$sqlQuery->from = Array($tableName);
		if($where) {
			$sqlQuery->where = array($where);
		}
		if($orderby) {
			$sqlQuery->orderby = $orderby;
		}
		$result = $sqlQuery->execute();
		return $result;
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.AddSubRegion", new CheckboxField("CreateChildren", "Create Child Pages (e.g. countries below continents)"));
		$fields->addFieldToTab("Root.Content.AddSubRegion", new CheckboxField("CreateAllChildren", "Create All Child Pages (e.g. countries, regions, and cities below continents)"));
		//$fields->addFieldToTab("Root.Content.AddSubRegion", new ReadonlyField("HiddenDataID", "Data ID number (should have number)"));
		$fields->addFieldToTab("Root.Content.AddSubRegion", new LiteralField("build", "Create now... ", '<a href="db/build/?flush=1&geobuild=1">create pages now</a>'));
		if(!$this->allowBrowseChildren()) {
			$fields->removeFieldFromTab("Root.Content.AddSubRegion", "CreateChildren");
			$fields->removeFieldFromTab("Root.Content.AddSubRegion", "CreateAllChildren");
		}

		$fields->addFieldsToTab('Root.Content.Main', new TextField('AlternativeURL', 'Alternative URL'));

		return $fields;
	}

	function onBeforeWrite() {
		if($this->CreateAllChildren) {
			$this->CreateChildren = 1;
		}
		return parent::onBeforeWrite();
	}

}

class BrowseAbstractPage_Controller extends Page_Controller {

	function init() {
		parent::init();
	}


}

