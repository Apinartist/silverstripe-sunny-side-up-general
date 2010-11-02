<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@package SlideShow
 *@description: this class holds "slides" for the slideshow
 **/

class SlideShowObject extends DataObject {

	public static $db = array(
		"Title" => "Varchar(125)",
		"Description" => "Text"
	);

	public static $belongs_many_many = array(
		"Parent" => "SiteTree",
	);

	public static $has_one = array(
		"Link" => "Page",
		"Image" => "Image"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
	 	$fields->removeFieldFromTab("Root.Main", "ParentID");
	 	$fields->removeFieldFromTab("Root.Main", "LinkID");
	 	$fields->removeFieldFromTab("Root", "Parent");
		$fields->addFieldToTab("Root.Main",new HiddenField("ParentID"));
		$treeField = new TreeMultiselectField("Parent", "Shown on the following pages", "SiteTree");
		$callback = $this->callbackFilterFunctionForMultiSelect();
		if($callback) {
			$treeField->setFilterFunction ($callback);
		}

		$fields->addFieldToTab("Root.Main",$treeField);
		return $fields;
	}

	function fieldLabels() {
		return array(
			'Title' => 'Title',
			'LinkID' => 'Link to page within website'
		);
	}
	public function summaryFields() {
		return $this->fieldLabels();
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}


	protected function callbackFilterFunctionForMultiSelect() {
		$inc = SlideShowDecorator::get_page_classes_with_slideshow();
		$exc = SlideShowDecorator::get_page_classes_without_slideshow();
		if(is_array($inc) && count($inc)) {
			$string = 'return in_array($obj->class, array(\''.implode("','", $inc).'\'));';
		}
		elseif(is_array($exc) && count($exc)) {
			$string = 'return !in_array($obj->class, array(\''.implode("','", $exc).'\'));';
		}
		if($string) {
			return create_function('$obj', $string);
		}
		else {
			return false;
		}
	}
}


class SlideShowObject_Admin extends ModelAdmin {
  public $showImportForm = false;
  public static $managed_models = array(
      'SlideShowObject'
   );
  static $url_segment = 'slideshow';
  static $menu_title = 'Slides';

}
