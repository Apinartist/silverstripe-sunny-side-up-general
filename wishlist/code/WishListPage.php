<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

class WishListPage extends Page {

	static $icon = "wishlist/images/treeicons/WishListPage";

	static $db = array(
		"AddedToListText" => "Varchar(255)",
		"CouldNotAddedToListText" => "Varchar(255)",
		"RemovedFromListConfirmation" => "Varchar(255)",
		"RetrieveListConfirmation" => "Varchar(255)",
		"RemovedFromListText" => "Varchar(255)",
		"CouldNotRemovedFromListText" => "Varchar(255)",
		"ClearWishList" => "Varchar(255)",
		"SavedWishListText" => "Varchar(255)",
		"SavedErrorWishListText" => "Varchar(255)",
		"RetrievedWishListText" => "Varchar(255)",
		"RetrievedErrorWishListText" => "Varchar(255)"
	);


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "AddedToListText", $title = "added to list"));
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "CouldNotAddedToListText", $title = "could not add to list"));
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "RemovedFromListText", $title = "removed from list"));
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "CouldNotRemovedFromListText", $title = "could not remove from list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "ClearWishList", $title = "cleared list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "SavedWishListText", $title = "saved list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "SavedErrorWishListText", $title = "could not save list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "RetrievedWishListText", $title = "retrieved list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "RetrievedErrorWishListText", $title = "could not retrieve list"));
		$fields->addFieldToTab("Root.Content.DoubleChecksQuestions", new TextField($name = "RemovedFromListConfirmation", $title = "Are you sure you want to remove item? Pop-up double-check question..."));
		$fields->addFieldToTab("Root.Content.DoubleChecksQuestions", new TextField($name = "RetrieveListConfirmation", $title = "Are you sure you want to retrieve your save list list? Pop-up double-check question..."));
		return $fields;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$update = array();
		$page = DataObject::get_one("WishListPage");

		if(!$page) {
			$page = new WishListPage();
			$page->Title = "Wish List";
			$page->MetaTitle = "Wish List";
			$page->URLSegment = "wish-list";
			$page->MenuTitle = "wish list";
		}
		if($page) {
			if(!$page->AddedToListText){$page->AddedToListText = "added to wish list"; $update[] ="updated AddedToListText";}
			if(!$page->CouldNotAddedToListText){$page->CouldNotAddedToListText = "could not add to wish list"; $update[] ="updated CouldNotAddedToListText";}
			if(!$page->RemovedFromListConfirmation){$page->RemovedFromListConfirmation = "are you sure you want to remove it from your wish list?"; $update[] ="updated RemovedFromListConfirmation";}
			if(!$page->RetrieveListConfirmation){$page->RetrieveListConfirmation = "Are you sure you would like to retrieve your saved list?  It will replace your current list.  Do you want to go ahead?"; $update[] ="updated RetrieveListConfirmation";}
			if(!$page->RemovedFromListText){$page->RemovedFromListText = "removed from wish list"; $update[] ="updated RemovedFromListText";}
			if(!$page->CouldNotRemovedFromListText){$page->CouldNotRemovedFromListText = "could not be removed from wish list"; $update[] ="updated CouldNotRemovedFromListText";}
			if(!$page->ClearWishList){$page->ClearWishList = "cleared wish list"; $update[] ="updated ClearWishList";}
			if(!$page->SavedWishListText){$page->SavedWishListText = "saved wish list"; $update[] ="updated SavedWishListText";}
			if(!$page->SavedErrorWishListText){$page->SavedErrorWishListText = "could not save wish list"; $update[] ="updated SavedErrorWishListText";}
			if(!$page->RetrievedWishListText){$page->RetrievedWishListText = "retrieved wish list"; $update[] ="updated RetrievedWishListText";}
			if(!$page->RetrievedErrorWishListText){$page->RetrievedErrorWishListText = "could not retrieve wish list"; $update[] ="updated RetrievedErrorWishListText";}
			if(count($update)) {
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				DB::alteration_message($page->ClassName." created/updated: ".implode("<li>", $update), 'created');
			}
		}
	}


}

class WishListPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		WishListDecorator_Controller::set_inline_requirements();
	}

	function WishList() {
		$stage = Versioned::current_stage();
		$baseClass = "SiteTree";
		$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
		$array = $this->wishListIDArray();
		return DataObject::get("$baseClass", "$stageTable.ID IN (".implode(",", $array).")");
	}

	function CanSaveWishList() {
		if($sessionOne = WishListDecorator_Controller::get_wish_list_from_session_serialized()) {
			if($savedOne = WishListDecorator_Controller::get_wish_list_from_member_serialized()) {
				if($savedOne == $sessionOne) {
					//can't save beceause there is nothing new to save...
					return false;
				}
			}
			return true;
		}
		return false;
	}

	function CanRetrieveWishList() {
		if($savedOne = WishListDecorator_Controller::get_wish_list_from_member_serialized()) {
			if($sessionOne = WishListDecorator_Controller::get_wish_list_from_session_serialized()) {
				if($savedOne == $sessionOne) {
					//can't retrieve beceause there is nothing new to retrieve...
					return false;
				}
			}
			return true;
		}
		return false;
	}

	function CanClearWishList() {
		if($savedOne = WishListDecorator_Controller::get_wish_list_from_member_serialized()) {
			return true;
		}
		if($sessionOne = WishListDecorator_Controller::get_wish_list_from_session_serialized()) {
			return true;
		}
		return false;
	}

	protected function wishListIDArray() {
		$array = WishListDecorator_Controller::get_wish_list_from_session_array();
		if(!is_array($array)) {
			$array = array();
		}
		$array[0] = 0;
		return $array;
	}

}
