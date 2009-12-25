<?php

/*
 * @author: nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

class SearchPlusSearchForm extends Extension {


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
			$form = new SearchForm($this, 'SearchForm', $fields, $actions);

			$form->setFormAction($page->Link()."results");
			return $form;
		}
		elseif(!$page) {
			user_error("You need to create a SearchPlusPage to have a search box", E_USER_NOTICE);
		}
	}


 }