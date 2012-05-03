<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: more details per template. Records are auto-added
 *@link: http://localhost/www.mysite.co.nz/admin/templates/TemplateOverviewDescription/13/edit
 **/


class TemplateOverviewDescription extends DataObject {

	static $db = array(
		"Description" => "Text",
		"ToDoListHyperLink" => "Varchar(255)",
		"ClassNameLink" => "Varchar(120)"
	);

	static $has_one = array(
		"Parent" => "TemplateOverviewPage",
		"Image1" => "Image",
		"Image2" => "Image",
		"Image3" => "Image",
		"Image4" => "Image",
		"Image5" => "Image",
		"Image6" => "Image",
		"Image7" => "Image"
	);

	static $belongs_many_many = array(
		"TemplateOverviewTestItems" => "TemplateOverviewTestItem"
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
		"ToDoListHyperLink" => "Link to To Do List (e.g. http://www.my-project-management-tool.com/mypage/)",
	);

	static $singular_name = 'Template Description';

	static $plural_name = 'Template Descriptions';

	static $default_sort = 'ClassNameLink ASC';

	/* CURRENTLY CREATES AN ERROR
	static $indexes = array(
		"ClassNameLinkUnique" => "unique('ClassNameLink')",
	);
	*/
	static $indexes = array(
		"ClassNameLink" => true,
	);

	function canAdd() {
		return false;
	}

	function canDelete() {
		return true;
	}

	function ClassNameLinkFancy() {
		return implode(" ", preg_split('/(?<=\\w)(?=[A-Z])/', $this->ClassNameLink));
		return preg_replace("/(?<=[^A-Z])([A-Z])/", "$1", $this->ClassNameLink);
	}

	function Title() {
		return $this->ClassNameLinkFancy();
	}

	function ModelAdminLink() {
		return TemplateOverviewDescriptionModelAdmin::get_full_url_segment()."/".$this->ClassName."/".$this->ID."/edit/";
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
		$fields->addFieldToTab("Root.Design", new ImageField("Image6", "Design Three", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image3", "Instructions One", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image4", "Instructions Two", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image5", "Instructions Three", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Instructions", new ImageField("Image7", "Instructions Four", $value = null, $form = null, $rightTitle = null, $folderName = "templateoverview/designs"));
		$fields->addFieldToTab("Root.Main", new HeaderField("ClassNameLinkInfo", "Details for: ".$this->ClassNameLink), "Description");
		$fields->addFieldToTab("Root.Main", new LiteralField("BackLink", '<p><a href="'.$page->Link().'#sectionFor-'.$this->ClassNameLink.'">go back to template overview page</a> - dont forget to SAVE FIRST.</p>'));
		$fields->removeByName("ParentID");
		return $fields;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$data = ClassInfo::subclassesFor("SiteTree");
		$templateOverviewPage = DataObject::get_one("TemplateOverviewPage");
		if($data && $templateOverviewPage) {
			foreach($data as $className) {
				if(!DataObject::get_one("TemplateOverviewDescription", "ClassNameLink = '$className'")) {
					$new = new TemplateOverviewDescription();
					$new->ClassNameLink = $className;
					$new->ParentID = $templateOverviewPage->ID;
					$new->write();
					DB::alteration_message("adding template description for $className");
				}
			}
		}
	}

	protected function validate() {
		if($this->ID) {
			if(DataObject::get_one("TemplateOverviewDescription", "ClassNameLink = '".$this->ClassNameLink."' AND ID <> ".$this->ID)) {
				return new ValidationResult(false, _t("TemplateOverviewDescription.ALREADYEXISTS", "This template already exists"));
			}
		}
		return new ValidationResult();
	}

	function onBeforeWrite() {
		if(!$this->ParentID) {
			if($page = DataObject::get_one("TemplateOverviewPage")) {
				$this->ParentID = $page->ID;
			}
		}
		parent::onBeforeWrite();
	}


}
