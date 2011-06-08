<?php


class DesignersPage extends Page {

	public static $icon = "designers/images/treeicons/DesignersPage";

	public static $has_many = array(
		"Designers" => "Designer"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab(
			"Root.Content.Designers"
			new HasManyComplexTableField(
				$controller = $this,
				$name = "Designers",
				$sourceClass = "Designer",
				$fieldList = null,
				$detailFormFields = null,
				$sourceFilter = "",
				$sourceSort = "",
				$sourceJoin = ""
			)
		);
		return $fields;
	}

}

class DesignersPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		PrettyPhoto::set_theme("dark_rounded");//"dark_rounded", "dark_square", "facebook", "light_rounded", "light_square" OR create your own!
		PrettyPhoto::set_more_config("AnimationSpeed: 'slow', opacity: 0.30, showTitle: false");//see readme for some examples!
	}

}