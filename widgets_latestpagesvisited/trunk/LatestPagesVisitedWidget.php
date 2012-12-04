<?php

class LatestPagesVisitedWidget extends Widget {

	static $db = array();

	static $number_of_pages_back = 21;

	static function setNumberOfPagesBack($val) {
		if($val > 0 && $val < 99) {
			self::$number_of_pages_back = $val;
		}
	}

	static $title = "Latest Pages Visited";
	static $cmsTitle = "Latest Pages Visited";
	static $description = "Shows the latest pages visited by the user in reverse chronological order";

	static function addPage() {
		$historyString = Session::get("LastPagesVisited");
		$historyArray = unserialize($historyString);
		if(!is_array($historyArray)) {
			$historyArray = Array();
		}
		$page = Director::currentPage();
		if($page) {
			$pageID = $page->ID;
			if($pageID) {
				if(!in_array($pageID, $historyArray)) {
					array_unshift($historyArray,$pageID);
					if(count($historyArray) > self::$number_of_pages_back) {
						array_pop($historyArray);
					}
				}
				elseif($historyArray[count($historyArray)-1] == $pageID) {
					array_pop($historyArray);
					array_unshift($historyArray,$pageID);
				}
			}
		}
		Session::set("LastPagesVisited", serialize($historyArray));
	}

	static function history ($pagesBack = 1) {
		$array = unserialize(Session::get("LastPagesVisited"));
		return $array[$pagesBack];
	}

	function LatestPages() {
		//copy from Director
		$historyArray = unserialize(Session::get("LastPagesVisited"));
		//Session::addToArray('history', substr($_SERVER['REQUEST_URI'], strlen(Director::baseURL())));
		$DOS = new DataObjectSet();
		//note that we go beyond number_of_pages_back as people may visit the same page twice
		for($i = 1; $i < self::$number_of_pages_back; $i++) {
			if(isset($historyArray[$i])) {
				$pageID = $historyArray[$i];
				if(intval($pageID)) {
					$page = DataObject::get_by_id("SiteTree", $pageID);
					if($page) {
						$DOS->push($page);
					}
				}
			}
		}
		return $DOS;

	}
}
