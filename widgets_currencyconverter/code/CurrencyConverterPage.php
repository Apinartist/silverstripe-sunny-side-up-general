<?php

class CurrencyConverterPage extends Page {

	static $icon = "widgets_currencyconverter/images/treeicons/CurrencyConverterPage";

	static $has_one = array(
		"MainContent" => "WidgetArea",
	);

	public function canCreate() {
		return !DataObject::get_one("CurrencyConverterPage");
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}


}

class CurrencyConverterPage_Controller extends Page_Controller {
	function init() {
		parent::init();
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript("widgets_currencyconverter/javascript/CurrencyConverter.js");
		Requirements::css("widgets_currencyconverter/css/CurrencyConverter.css");
	}
	function index() {
		if(Director::is_ajax() || isset($_GET["ajax"])) {
			return $this->renderWith("CurrencyConverterAjax");
		}
	}
	function CurrencyConverterWidget() {
		$widget = new CurrencyConverterWidget();
		return $widget->renderWith("WidgetHolderMysite");
	}
}

