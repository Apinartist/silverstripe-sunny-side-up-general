<?php

/*
 * @author: nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

 class SearchFormPlus extends Extension {


	function SearchForm() {
		$action = Director::URLParam("Action");
		$page = DataObject::get_one("SearchPlusPage");
		if(!in_array($action, array("login", "logout")) && $page) {
			$searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : 'Search';
			$fields = new FieldSet(
				new TextField('Search', '', $searchText)
			);
			$actions = new FieldSet(
				new FormAction('results', 'Search')
			);
			$form = SearchForm($this, 'SearchForm', $fields, $actions);

			$form->setFormAction($page->Link());
			return $form;
		}
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