<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: adds functionality to controller for dev purposes only
 *
 **/


class TemplateOverviewPageDecorator extends DataObjectDecorator {

	protected static $help_file_directory_name = "_help";
		static function set_help_file_directory_name($s) {self::$help_file_directory_name = $s;}
		static function get_help_file_directory_name() {return self::$help_file_directory_name;}

	function updateCMSFields(&$fields) {
		if(method_exists($this->owner,'getHowToMakeThisTemplateWorkArray')) {
			$array = $this->owner->getHowToMakeThisTemplateWorkArray();
			if(is_array($array) && count($array)) {
				$fields->addFieldToTab("Root.Help", new LiteralField(
					"HowToMakeThisPageWork",
					'<h3 id="HowToMakeThisPageWorkHeader">'._t("TemplateOverviewPageDecorator.HOWTOMAKEITWORKHEADER", "How to make this page work").'</h3>'
						.'<ul id="HowToMakeThisPageWorkList"><li>'.implode("</li><li>",$array).'</li></ul>'
				));
			}
		}
		$obj = DataObject::get_one("TemplateOverviewDescription", "ClassNameLink = '".$this->owner->ClassName."'");
		if($obj) {
			if($obj->ToDoListHyperLink) {
				$fields->replaceField("ToDo", new LiteralField("ToDo", '<p><a href="'.$obj->ToDoListHyperLink.'" target="todo">review to do items...</a></p>'));
			}
			$fields->addFieldToTab("Root.Help", new LiteralField("MoreHelp", $obj->renderWith("TemplateOverviewPageCMSDetail")));
		}
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$directory = Director::baseFolder()."/".self::get_help_file_directory_name()."/";
		if(is_dir($directory)) {
			//get all image files with a .jpg extension.
			$images = $this->getDirectoryList($directory , array("png", "jpg"));
			//print each file name
			if($images && count($images)) {
				$newDirectory = Director::baseFolder()."/assets/help".self::get_help_file_directory_name()."/";
				Filesystem::makeFolder($newDirectory);
				foreach($images as $image){
					if(file_exists($directory.$image) && !file_exists($newDirectory.$image)) {
						copy($directory.$image, $newDirectory.$image);
					}
				}
			}
		}
	}

	private function getDirectoryList ($directory, $extensionArray) {
		//	create an array to hold directory list
		$results = array();
		// create a handler for the directory
		$handler = opendir($directory);
		// open directory and walk through the filenames
		while ($file = readdir($handler)) {
			// if file isn't this directory or its parent, add it to the results
			if ($file != "." && $file != "..") {
				$extension = substr(strrchr($file, '.'), 1);
				if(in_array($extension, $extensionArray)) {
					$results[] = $file;
				}
			}
		}
	// tidy up: close the handler
		closedir($handler);
		// done!
		return $results;
	}


}
