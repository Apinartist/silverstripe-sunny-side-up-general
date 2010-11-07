<?php


class AdvertisementController extends Controller {

	static $allowed_actions = array("removealladvertisements");

	function removealladvertisements($request) {
		$member = Member::currentMember();
		if(!Permission::checkMember($member, "CMS_ACCESS_LeftAndMain")) {
			return "you do not have permission to delete these advertisements.";
		}
		$id = $request->param("ID");
		$page = DataObject("SiteTree", intval($id));
		if(!$page) {
			return "this page does not exist";
		}
		DB::query("DELETE FROM SiteTree_Advertisement WHERE SiteTreeID = ".$id);
		DB::query("UPDATE SiteTree SET AdvertisementFolderID = 0 WHERE SiteTree.ID = ".$id);
		DB::query("UPDATE SiteTree_Live SET AdvertisementFolderID = 0 WHERE SiteTree_Live.ID = ".$id);
		return "deleted all advertisements for this page, reload page to see results ...";
	}
}
