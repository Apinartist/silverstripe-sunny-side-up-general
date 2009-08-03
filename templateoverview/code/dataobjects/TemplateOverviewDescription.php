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


	public function getCMSFields() {
		$fields = parent::getCMSFields();
	 	return $fields;
	}


	static $searchable_fields = array('Description', 'ClassNameLink');

	static $summary_fields = array('Description', 'ClassNameLink');

	static $singular_name = 'Template Description';

	static $plural_name = 'Template Descriptions';

	static $default_sort = 'ClassNameLink ASC';

	static onBeforeWrite() {
		if(!$this->ParentID) {
			if($page = DataObject::get_one("TemplateOverviewPage")) {
				$this->ParentID = $page->ID;
			}
		}
		parent::onBeforeWrite();
	}

}