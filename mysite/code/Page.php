<?php

class Page extends SiteTree {

	function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

}

class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		$this->addBasicMetatagRequirements();
	}

	function SearchForm() {
		$searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : 'Search';
		$fields = new FieldSet(
			new TextField("Search", "", $searchText)
		);
		$actions = new FieldSet(
			new FormAction('results', 'Search')
		);
  	return new SearchForm($this, "SearchForm", $fields, $actions);
	}

	function results($data, $form){
		$data = array(
			'Results' => $form->getResults(),
			'Query' => $form->getSearchQuery(),
			'Title' => 'Search Results'
		);
		return $this->customise($data)->renderWith(array('Page_results', 'Page'));
	}

}

