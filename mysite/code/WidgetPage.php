<?php

class WidgetPage extends Page {

	static $icon = "mysite/images/treeicons/WidgetPage";

	static $has_one = array(
		"Sidebar" => "WidgetArea",
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("Sidebar"));
		return $fields;
	}

}

class WidgetPage_Controller extends Page_Controller {
}
