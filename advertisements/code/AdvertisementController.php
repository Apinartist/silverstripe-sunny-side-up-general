<?php


class AdvertisementController extends Controller {

	static $allowed_actions = array(
		"removealladvertisements" => "ADMIN",
		"deletealladvertisements" => "ADMIN"
	);

	function removealladvertisements($request) {
		$id = intval($request->param("ID"))-0;
		$page = DataObject::get_by_id("SiteTree", $id);
		if(!$page) {
			return "this page does not exist";
		}
		DB::query("DELETE FROM SiteTree_Advertisements WHERE SiteTreeID = ".$id);
		DB::query("UPDATE SiteTree SET AdvertisementsFolderID = 0 WHERE SiteTree.ID = ".$id);
		DB::query("UPDATE SiteTree_Live SET AdvertisementsFolderID = 0 WHERE SiteTree_Live.ID = ".$id);
		LeftAndMain::ForceReload();
		return "deleted all ".Advertisement::$plural_name." for this page, reload page to see results ...";
	}

	function deletealladvertisements($request) {
		DB::query("DELETE FROM Advertisement");
		LeftAndMain::ForceReload();
		return "Deleted all ".Advertisement::$plural_name." on the website!";
	}

}
