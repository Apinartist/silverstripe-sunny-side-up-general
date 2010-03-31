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
		$id = intval(Director::URLParam("ID"));
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$ecommerceVote = new EcommerceVote();
				$ecommerceVote->PageID = $id;
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


	function TopEcommerceVotes($numberOfEntries = 5) {
		$sqlQuery = new SQLQuery(
			$select = "SiteTree.ID MyPageID, COUNT(EcommerceVote.ID) c",
			$from = array('SiteTree INNER JOIN EcommerceVote ON SiteTree.ID = EcommerceVote.PageID'),
			$where = "",
			$orderby = "c",
			$groupby = "SiteTree.ID",
			$having = "",
			$limit = "0, $numberOfEntries"
		);
		$results = $sqlQuery->execute();
		if($results) {
			$stage = Versioned::current_stage();
			$baseClass = "SiteTree";
			$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
			$array = array();
			$array[0] = 0;
			foreach($results as $result) {
				$array[$result["MyPageID"]] = $result["MyPageID"];
			}
			if(count($array)) {
				return DataObject::get("SiteTree", "$stageTable.ID IN (".implode($array).")");
			}
		}
	}

	function EcommerceVoteTopFive() {
		return $this->TopEcommerceVotes(5);
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$objects = DataObject::get("EcommerceVote");
		$array = array();
		if($objects) {
			foreach($objects as $obj) {
				if(isset($array[$obj->PageID]) && $array[$obj->PageID] == $obj->SessionID) {
					$obj->delete();
					Database::alteration_message("deleting double vote", "deleted");
				}
				$array[$obj->PageID] = $obj->SessionID;
			}
		}
	}

}