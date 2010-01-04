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
		"Image3" => "Image",
		"Image4" => "Image",
		"Image5" => "Image"
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
		$page = DataObject::get_one("TemplateOverviewPage");
		if(!$page) {
			user_error("Please make sure to create a TemplateOverviewPage to make use of this module.", E_USER_WARNING);
		}
		$fields->removeByName("ClassNameLink");
		$fields->removeByName("Image1");
		$fields->removeByName("Image2");
		$fields->removeByName("Image3");
		$fields->removeByName("Image4");
		$fields->removeByName("Image5");
		$fields->addFieldToTab("Root.Design", new ImageField("Image1", "Design One", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Design", new ImageField("Image2", "Design Two", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image3", "Instructions One", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image4", "Instructions Two", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image5", "Instructions Three", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Main", new HeaderField("ClassNameLinkInfo", "Details for: ".$this->ClassNameLink), "Description");
		$fields->addFieldToTab("Root.Main", new LiteralField("BackLink", '<p><a href="'.$page->Link().'#sectionFor-'.$this->ClassNameLink.'">go back to template overview page</a> - dont forget to SAVE FIRST.</p>'));
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