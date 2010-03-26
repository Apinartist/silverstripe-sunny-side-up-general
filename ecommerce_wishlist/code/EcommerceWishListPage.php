<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

class EcommerceWishListPage extends Page {

	static $icon = "mysite/images/treeicons/EcommerceWishListPage";

	static $db = array(
		"AddedToListText" => "Varchar(255)",
		"CouldNotAddedToListText" => "Varchar(255)",
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
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "AddedToListText", $title = "Added to list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "CouldNotAddedToListText", $title = "could not add to list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "RemovedFromListText", $title = "removed from list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "CouldNotRemovedFromListText", $title = "could not remove from list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "ClearWishList", $title = "cleared list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "SavedWishListText", $title = "saved list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "SavedErrorWishListText", $title = "could not save list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "RetrievedWishListText", $title = "retrieved list"));
		$fields->addFieldToTab("Root.Content.WishListFeedbackMessages", new TextField($name = "RetrievedErrorWishListText", $title = "could not retrieve list"));

		return $fields;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$update = '';
		$page = DataObject::get_one("EcommerceWishListPage");

		if(!$page) {
			$page = new EcommerceWishListPage();
			$page->Title = "Wish List";
			$page->MetaTitle = "Wish List";
			$page->URLSegment = "wish-list";
			$page->MenuTitle = "wish list";
		}
		if($page) {
			if(!$page->AddedToListText){$page->AddedToListText = "added to wish list"; $update .= "updated AddedToListText, ";}
			if(!$page->CouldNotAddedToListText){$page->CouldNotAddedToListText = "could not add to wish list"; $update .= "updated CouldNotAddedToListText, ";}
			if(!$page->RemovedFromListText){$page->RemovedFromListText = "removed from wish list"; $update .= "updated RemovedFromListText, ";}
			if(!$page->CouldNotRemovedFromListText){$page->CouldNotRemovedFromListText = "could not be removed from wish list"; $update .= "updated CouldNotRemovedFromListText, ";}
			if(!$page->ClearWishList){$page->ClearWishList = "cleared wish list"; $update .= "updated ClearWishList, ";}
			if(!$page->SavedWishListText){$page->SavedWishListText = "saved wish list"; $update .= "updated SavedWishListText, ";}
			if(!$page->SavedErrorWishListText){$page->SavedErrorWishListText = "could not save wish list"; $update .= "updated SavedErrorWishListText, ";}
			if(!$page->RetrievedWishListText){$page->RetrievedWishListText = "retrieved wish list"; $update .= "updated RetrievedWishListText, ";}
			if(!$page->RetrievedErrorWishListText){$page->RetrievedErrorWishListText = "could not retrieve wish list"; $update .= "updated RetrievedErrorWishListText, ";}
			if($update) {
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				Database::alteration_message($page->ClassName." created/updated: ".$update, 'created');
			}
		}
	}


}

class EcommerceWishListPage_Controller extends Page_Controller {


	function test() {
		return $page->renderWith("EcommerceWishListTest");
	}

}