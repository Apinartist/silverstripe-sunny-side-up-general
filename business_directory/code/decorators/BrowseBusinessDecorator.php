<?php

class BrowseBusinessDecorator extends DataObjectDecorator {

	static $max_radius = 100;

	function __construct () {
		parent::__construct();
	}

	function setSidebarImage() {
		return false;
	}

}

class BrowseBusinessDecorator_Controller extends Extension {

	function index() {
		$this->owner->addCustomMap($this->owner->Children()); //DataObject::get("BusinessPage")
		return Array();
	}

	/*
		This function should be overriden in page-types where not random image is required
	*/

	function SidebarImage() {
		return false;
	}

	public function NewListingForm() {
		if("BrowseCitiesPage" == $this->owner->ClassName) {
			$fields = new FieldSet(
				 new TextField($name = "NewListingName", $title = "New Listing Name"),
				 new AddressFinderField($name = "NewListingAddress", $title = "Location for listing")
			);
			$actions = new FieldSet (
				new FormAction("doNewListingForm", "Create Listing")
			);
			$validator = new RequiredFields("NewListingName", "NewListingAddress");
			return new Form($this->owner, "newlistingform", $fields, $actions, $validator );
		}
		else {
			die("not good");
		}
		return array();
	}

	public function doNewListingForm($data, $form) {
		$member = Member::currentUser();
		if(DataObject::get_one("SiteTree", 'SiteTree_Live.Title = "'.Convert::raw2sql($data["NewListingName"]).'"')) {
			$form->addErrorMessage('NewListingName','Sorry, but a listing with that name already exists', "bad");
			Director::redirectBack();
		}
		elseif(!$member) {
			$form->addErrorMessage('NewListingAddress','You must be logged-in to create a new listing', "bad");
			Director::redirectBack();
		}
		else {
			$stringDO = new SearchStringDataObject();
			$stringDO->string = $data["NewListingAddress"];
			$addressArray = $form->dataFieldByName("NewListingAddress")->getAddressArray();
			if(GoogleMapLocationsObject::pointExists($addressArray)) {
				$form->addErrorMessage('NewListingAddress','This address already exists for another listing.  Please check the existing listings first to prevent double-ups OR use a more specific address.', "bad");
				Director::redirectBack();
			}
			if($addressArray) {
				$stringDO->Success = 1;
				$stringDO->write();
				$city = BrowseCitiesPage::get_clostest_city_page($addressArray, BrowseBusinessDecorator::$max_radius);
				if($city) {
					if($city->ID == $this->owner->ID) {
						$page = new BusinessPage();
						$page->Title = $data["NewListingName"];
						$page->ToDo = "check";
						$page->Email = $member->Email;
						$page->ParentID = $this->owner->ID;
						$page->writeToStage('Stage');
						$page->publish('Stage', 'Live');
						$page->flushCache();

						$page->Members()->add($member);
						Group::addToGroupByName($member, 'business-members');

						$point = new GoogleMapLocationsObject();
						$point->addDataFromArray($addressArray);
						$point->ParentID = $page->ID;
						$point->write();

						Director::redirect(Director::absoluteBaseURL().$page->URLSegment);

					}
					else {
						$form->addErrorMessage('NewListingAddress','Sorry, this address, <address>'.$addressArray["address"].'</address>, is closer '.$city->Title.' than to '.$this->owner->Title.'.', "bad");
						Director::redirect($city->Link());
					}
				}
			}
			$stringDO->write();
			$form->addErrorMessage('Address','Sorry, no city cound be found for your address: <address>'.$addressArray["address"].'</address>', "bad");
			return array();
		}
	}

}


