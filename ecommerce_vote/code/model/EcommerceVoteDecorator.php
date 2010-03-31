<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/


class EcommerceVoteDecorator extends Extension {


	static $allowed_actions = array(
		"addecommercevote" => true
	);


	protected static $session_variable_name = "EcommerceVoteDecorator";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}


	function addecommercevote() {
		$id = $this->getIDForEcommerceVote();
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$ecommerceVote = new EcommerceVote();
				$ecommerceVote->PageID = $page->ID;
				$ecommerceVote->write();
				if(Director::is_ajax()) {
					return "voted";
				}
				else {
					Director::redirectBack();
					return;
				}
			}
		}
		if(Director::is_ajax()) {
			return "vote ERROR";
		}
		else {
			Director::redirectBack();
			return;
		}
	}

	protected function getIDForEcommerceVote() {
		//check URL Param
		$id = intval(Director::URLParam("ID"));
		if(!$id) {
			$id = $this->owner->ID;
		}
		return $id;
	}

	function TopEcommerceVotes($numberOfEntries = 5) {
		$sqlQuery = new SQLQuery(
			$select = "SiteTree.ID MyPageID, Count(EcommerceVote.ID) count",
			$from = array('SiteTree INNER JOIN SiteTree On EcommerceVote.PageID =  SiteTree.ID'),
			$where = "",
			$orderby = "Count(EcommerceVote.ID)",
			$groupby = "SiteTree.ID",
			$having = "",
			$limit = "0, $numberOfEntries"
		);
		$results = $sqlQuery->execute();
		if($results) {
			$array = array();
			foreach($results as $result) {
				$array[$result["MyPageID"]] = $result["MyPageID"];
			}
			if(count($array)) {
				return DataObject::get("SiteTree", "ID IN (".implode($array).")");
			}
		}
	}


}