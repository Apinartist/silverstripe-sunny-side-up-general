<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: more details per template. Records are auto-added
 *@link: http://localhost/kitesurf.co.nz/trunk/admin/templates/TemplateOverviewDescription/13/edit
 **/


class TemplateOverviewDescription extends DataObject {

	static $db = array(
		"Description" => "Text",
		"ToDoListHyperLink" => "Varchar(255)",
		"ClassNameLink" => "Varchar(255)"
	);

	static $has_one = array(
		"Parent" => "TemplateOverviewPage",
		"Image1" => "Image",
		"Image2" => "Image",
		"Image3" => "Image"
	);

	public static $searchable_fields = array(
		"ClassNameLink" => "PartialMatchFilter"
	);

	public static $summary_fields = array(
		"ClassNameLink",
		"ToDoListHyperLink"
	);

	public static $field_labels = array(
		"ClassNameLink" => "Page Type Name",
		"ToDoListHyperLink" => "Link to ToDo List",
	);

	static $singular_name = 'Template Description';

	static $plural_name = 'Template Descriptions';

	static $default_sort = 'ClassNameLink ASC';

	function canAdd() {
		return false;
	}

	function canDelete() {
		return false;
	}

	function onBeforeWrite() {
		if(!$this->ParentID) {
			if($page = DataObject::get_one("TemplateOverviewPage")) {
				$this->ParentID = $page->ID;
			}
		}
		parent::onBeforeWrite();
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("ClassNameLink");
		$fields->addFieldToTab("Root.Link", new HeaderField("ClassNameLinkInfo", $this->ClassNameLink));
		$fields->removeByName("ParentID");
		return $fields;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$data = ClassInfo::subclassesFor("SiteTree");
		if($data) {
			foreach($data as $className) {
				if(!DataObject::get_one("TemplateOverviewDescription", "ClassNameLink = '$className'")) {
					$new = new TemplateOverviewDescription();
					$new->ClassNameLink = $className;
					$new->write();
				}
			}
		}
	}

}