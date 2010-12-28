<?php


class AddBusinessPage extends Page {

	static $icon = "AddBusinessPage";

}

class AddBusinessPage_Controller extends Page_Controller {

	public function NewListingForm() {
		$fields = new FieldSet(
			 new TextField($name = "NewListingName", $title = "New Listing Name"),
			 new AddressFinderField($name = "NewListingAddress", $title = "Location for listing")
		);
		$actions = new FieldSet (
			new FormAction("donewlistingform", "Create Listing")
		);
		$validator = new RequiredFields("NewListingName", "NewListingAddress");
		return new Form($this->owner, "newlistingform", $fields, $actions, $validator );
	}

	public function donewlistingform($data, $form) {
		//add new search record here....
		$member = Member::currentUser();
		if(DataObject::get_one("SiteTree", "\"SiteTree_Live\".\"Title\" = '".Convert::raw2sql($data["NewListingName"])."'")) {
			$form->addErrorMessage('NewListingName','Sorry, but a listing with that name already exists', "bad");
			Director::redirectBack();
			return;
		}
		elseif(!$member) {
			$form->addErrorMessage('NewListingAddress','You must be logged-in to create a new listing', "bad");
			Director::redirectBack();
			return;
		}
		else {
			$addressArray = $form->dataFieldByName("NewListingAddress")->getAddressArray();
			if(GoogleMapLocationsObject::pointExists($addressArray)) {
				$form->addErrorMessage('NewListingAddress','This address already exists for another listing.  Please check the existing listings first to prevent double-ups OR use a more specific address.', "bad");
				Director::redirectBack();
			}
			if($addressArray) {
				print_r($addressArray);
				return '<a href="'.$this->owner->Link('createnewbusinesslistingfrompoint').
					'/?address='.urlencode($addressArray["address"]).
					'&amp;name='.urlencode($data["NewListingName"]).'">confirm address</a> or
					<a href="'.$this->owner->Link().'">change address</a>';
			}
		}
	}

	function createnewbusinesslistingfrompoint($request) {
		if($request->param("ID") && isset($_GET["address"]) && isset($_GET["name"])) {
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
					Group::addToGroupByCode($member, 'business-members');
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
		else {
			Director::redirectBack();
		}
	}
}
