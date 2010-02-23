<?php
/**
 *
 *
 */
class FaqOnePage extends Page {

	static $icon = "mysite/images/treeicons/FaqOnePage";

	static $default_parent = 'FaqHolderPage';

	static $allowed_children = "none";

	static $db = array();

	static $has_one = array();


	//static $has_many = array();

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("Title", new TextField("Title", "Question"));
		$fields->replaceField("MenuTitle", new TextField("MenuTitle", "Question - short version for menus"));
		return $fields;
	}

}

class FaqOnePage_Controller extends Page_Controller {



	function init() {
		parent::init();
	}


	public function ShowMenu() {
		return true;
	}


	public function CacheLayout() {
		return true;
	}

}

