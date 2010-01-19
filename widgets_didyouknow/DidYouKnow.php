<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class DidYouKnow extends Widget {

	static $db = array(
		"Title" => "Varchar(50)"
	);

	static $title = 'Did You Know?';

	static $cmsTitle = 'Did You Know?';

	static $description = 'Allows you to add a random Did you know....? statement.';

	function getCMSFields() {
		return new FieldSet(
			new TextField("Title", "Title"),
			new TableField(
				$name = "DidYouKnow",
				$sourceClass = "DidYouKnow_Statement",
				$fieldList = array("Content" => "Content"),
				$fieldTypes = array("Content" => "TextField")
			)
		);
	}

	function Title() {
		return $this->Title ? $this->Title : self::$title;
	}

	function getTitle() {
		return $this->Title;
	}

	function RandomDidYouKnowItem() {
		Requirements::themedCSS("widgets_headlines");
		$do = DataObject::get_one("DidYouKnow_Statement", null, $cache = true, "RAND() DESC");
		if($do) {
			return $do->Content;
		}
	}

}

class DidYouKnow_Statement extends DataObject {

	$db = array(
		"Content" => "Varchar(255)"
	);


}