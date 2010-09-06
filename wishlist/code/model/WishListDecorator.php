<?php

class WishListDecorator_DataObject extends DataObjectDecorator {

	function IsOnWishList() {
		$array = WishListDecorator_Controller::get_wish_list_from_session();
		if(isset($array[$this->owner->ID])) {
			return true;
		}
		else {
			return false;
		}
	}



}

class WishListDecorator_Controller extends Extension {

	// ____ statics

	static $allowed_actions = array(
		"addtowishlist" => true,
		"removefromwishlist" => true,
		"clearwishlist" => true,
		"savewishlist" => true,
		"retrievewishlist" => true,
		"loadlist" => true
	);

	protected static $session_variable_name = "WishListDecoratorArray";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	protected static $cached_wish_list_array = null;
		protected static function set_cached_wish_list_array($v){self::$cached_wish_list_array = $v;}
		protected static function get_cached_wish_list_array () {return self::$cached_wish_list_array;}


	static function get_wish_list_from_session() {
		//store in static variable so that you do not have to retrieve all the time...
		if(!is_array(self::get_cached_wish_list_array())) {
			$array = Session::get(self::get_session_variable_name()."_data");
			//set static variable
			self::set_cached_wish_list_array($array);
		}
		//return static variable
		return unserialize(self::get_cached_wish_list_array());
	}

	static function set_wish_list_to_session($array) {
		//make sure it is an array
		if(!is_array($array)) {
			user_error("There is an error in storing your wish list, your variable should be an array", E_USER_WARNING);
		}
		else {
			//set session variable
			Session::clear(self::get_session_variable_name()."_data");
			Session::save();
			Session::set(self::get_session_variable_name()."_data", null);
			Session::save();
			Session::set(self::get_session_variable_name()."_data", serialize($array));
			Session::save();
		}
	}

	// ____ actions

	function addtowishlist() {
		$id = $this->getIDForWishList();
		$outcome = false;
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$outcome = true;
				$array = self::get_wish_list_from_session();
				$array[$id]= $id;
				self::set_wish_list_to_session($array);
			}
		}
		return $this->standardReturn($outcome, "AddedToListText", "CouldNotAddedToListText", "WishListLinkInner");
	}

	function removefromwishlist() {
		$id = $this->getIDForWishList();
		$outcome = false;
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$outcome = true;
				//get current wish list
				$array = self::get_wish_list_from_session();
				//remove from wish list
				unset($array[$id]);
				//reset
				self::set_wish_list_to_session($array);
			}
		}
		return $this->standardReturn($outcome, "RemovedFromListText", "CouldNotRemovedFromListText", "WishListLinkInner");
	}

	function savewishlist() {
		if($outcome = $this->CanSaveWishList()) {
			$member = Member::currentMember();
			$member->WishList = serialize(self::get_wish_list_from_session());
			$member->write();
		}
		return $this->standardReturn($outcome, "SavedWishListText", "SavedErrorWishListText", "WishListSaveAndRetrieveInner");
	}

	function retrievewishlist() {
		if($outcome = $this->CanRetrieveWishList()) {
			$member = Member::currentMember();
			self::set_wish_list_to_session(unserialize($member->WishList));
		}
		return $this->standardReturn($outcome, "RetrievedWishListText", "RetrievedErrorWishListText", "WishListListInner");
	}

	function loadlist() {
		if(Director::is_ajax()) {
			return $this->owner->renderWith("WishListLinkInner");
		}
	}

	function clearwishlist() {
		Session::set_wish_list_to_session(array());
		Session::save();
		return $this->standardReturn(true, "ClearWishList", "", "WishListSaveAndRetrieveInner");
	}

	// ____ template variables

	function WishList() {
		$confirmRetrieveText = $this->getVariableFromwishListPage("RetrieveListConfirmation");
		Requirements::customScript("WishList.set_confirm_retrieve_text('".Convert::raw2js($confirmRetrieveText)."')", "set_confirm_retrieve_text");
		Requirements::customScript("WishList.set_reload_list_url('".Convert::raw2js(Director::absoluteURL($this->owner->Link()."loadlist/"))."')", "set_reload_list_url");
		$array = self::get_wish_list_from_session();
		$stage = Versioned::current_stage();
		$baseClass = "SiteTree";
		$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
		$array[0] = 0;
		return DataObject::get("$baseClass", "$stageTable.ID IN (".implode(",", $array).")");
	}

	function CanSaveWishList() {
		return $this->WishList() && Member::currentMember();
	}

	function CanRetrieveWishList() {
		if($member = Member::currentMember()) {
			return $member->WishList;
		}
	}

	function WishListMessage() {
		$confirmDeleteText = $this->getVariableFromwishListPage("RemovedFromListConfirmation");
		Requirements::customScript("WishList.set_confirm_delete_text('".Convert::raw2js($confirmDeleteText)."')", "set_confirm_delete_text");
		//retrieve message
		$msg = Session::get(self::get_session_variable_name()."_message");
		//remove it from session
		Session::set(self::get_session_variable_name()."_message", null);
		//return it
		return $msg;
	}


	// ____ internal functions

	protected function getVariableFromwishListPage ($variableName) {return $this->wishListPage()->$variableName;}

	protected function getIDForWishList() {
		//check URL Param
		$id = intval(Director::URLParam("ID"));
		if(!$id) {
			$id = $this->owner->ID;
		}
		return $id;
	}


	protected function wishListPage() {
		return DataObject::get_one("wishListPage");
	}


	protected function standardReturn($outcome, $successMessageName, $errorMessageName, $template) {
		if($outcome) {
			Session::set(self::get_session_variable_name()."_message", $this->getVariableFromwishListPage($successMessageName));
			if(Director::is_ajax()) {
				return $this->owner->renderWith($template);
			}
			else {
				Session::save();
				Director::redirectBack();
				return;
			}
		}
		Session::set(self::get_session_variable_name()."_message", $this->getVariableFromwishListPage($errorMessageName));
		if(Director::is_ajax()) {
			return $this->owner->renderWith($template);
		}
		else {
			Session::save();
			Director::redirectBack();
			return;
		}
	}

}
