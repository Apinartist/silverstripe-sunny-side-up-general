<?php

class TemplateOverviewDescription extends DataObject {

	static $db = array(
		"Description" => "Text",
		"ClassNameLink" => "Varchar(255)"
	);

	static $has_one = array(
		"Parent" => "TemplateOverviewPage",
		"Image1" => "Image",
		"Image2" => "Image",
		"Image3" => "Image",
		"Image4" => "Image",
		"Image5" => "Image",
		"Image6" => "Image",
		"Image7" => "Image",
	);

	public static $searchable_fields = array("ClassNameLink" => "PartialMatchFilter");

	public static $summary_fields = array("ClassNameLink");

	public static $field_labels = array(
		"ClassNameLink" => "Page Type Name"
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
		$fields->addFieldToTab("Root.Content", new DropdownField("ClassNameLink", "Page Type", ClassInfo::subclassesFor("SiteTree")));
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