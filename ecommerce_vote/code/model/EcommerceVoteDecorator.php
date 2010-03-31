<?php



class EcommerceVoteDecorator extends Extension {

	protected static $session_variable_name = "EcommerceVoteDecorator";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	function HasEcommerceVote() {
		return DataObject::get_one("EcommerceVote", "SessionID = '".Session_ID()."' AND PageID = ".$this->owner->dataRecord->ID);
	}

	function addecommercevote() {
		$id = $this->getIDForEcommerceVote();
		if($id) {
			if($page = Director::get_by_id("SiteTree", $id)) {
				$ecommerceVote = new EcommerceVote();
				$ecommerceVote->PageID = $this->owner->dataRecord->ID;
				$ecommerceVote->write();
				if(Director::is_ajax()) {
					return "voted";
				}
				else {
					Director::redirectBack();
				}
			}
		}
		if(Director::is_ajax()) {
			return "vote ERROR";
		}
		else {
			Director::redirectBack();
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



}