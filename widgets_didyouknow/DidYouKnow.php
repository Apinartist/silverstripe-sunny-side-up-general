<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 **/
class DidYouKnow extends Widget {

	static $title = 'Did You Know?';

	static $cmsTitle = 'Did You Know?';

	static $description = 'Allows you to add a random Did you know....? statement.';

	function getCMSFields() {
		return new FieldSet(
			new TableField(
				$name = "DidYouKnow",
				$sourceClass = "DidYouKnow_Statement",
				$fieldList = array("Content" => "Content"),
				$fieldTypes = array("Content" => "TextField")
			)
		);
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