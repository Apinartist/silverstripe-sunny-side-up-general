<?php

class Page extends SiteTree {

	static $has_one = array(
		"Sidebar" => "WidgetArea",
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("Sidebar"));
		return $fields;
	}


	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!DataObject::get("Member")) {
			$member = new Member();
			$member->FirstName = "Sunny";
			$member->Surname = 'Tester';
			$member->Email = 'tester@sunnysideup.co.nz';
			$member->write();
			$member = DataObject::get_one("Member", "Email = 'tester@sunnysideup.co.nz'");
			if($member) {
				$member->Password = 'lovesSun33'; // support for up to SHA256!
				$member->RememberLoginToken = true;
			}
		}
		else {
			$member = DataObject::get_one("Member", "Email = 'tester@sunnysideup.co.nz'");
			$group = DataObject::get_by_id("Group", 1);
			if($group && $member) {
				if(!$member->IsAdmin()) {
					Group::addToGroupByName($member, $group->Code);
					$member->write();
				}
				if($dos = DataObject::get_one("Member", "Email <> 'tester@sunnysideup.co.nz'")) {
					if($member) {
						$member->delete();
					}
				}
			}
		}
		$page = DataObject::get_one("Page", "`URLSegment` = 'admin-only'");
		if(!$page) {
			$page = new Page();
			$page->URLSegment = "admin-only";
			$page->Title = "Admin Only";
			$page->ShowInMenus = 0;
			$page->ShowInSearch = 0;
			$page->writeToStage("Stage");
			$page->publish("Stage", "Live");
		}

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

