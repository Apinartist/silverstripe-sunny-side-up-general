<?php

class WishListDecorator_DataObject extends DataObjectDecorator {

	function IsOnWishList() {
		$array = WishListDecorator_Controller::get_wish_list_from_session_array();
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

	private static $requirements_added = false;

	protected static $session_variable_name = "WishListDecoratorArray";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	public static function get_wish_list_from_session_array() {
		//store in static variable so that you do not have to retrieve all the time...
		$string = self::get_wish_list_from_session_serialized();
		//set static variable
		return unserialize($string);
	}

	public static function get_wish_list_from_session_serialized() {
		$string = Session::get(self::get_session_variable_name()."_data");
		if(!is_string($string)) {
			$string = '';
		}
		if(!$string) {
			$string = self::get_wish_list_from_member_serialized();
			if($string) {
				$array = unserialize($string);
				self::set_wish_list_to_session_and_member($array);
			}
		}
		return $string;
	}

	public static function get_wish_list_from_member_array() {
		//store in static variable so that you do not have to retrieve all the time...
		$string = self::get_wish_list_from_member_serialized();
		//set static variable
		return unserialize($string);
	}

	public static function get_wish_list_from_member_serialized() {
		$member = Member::currentMember();
		$string = '';
		if($member) {
			$string = $member->WishList;
		}
		if(!is_string($string)) {
			$string = '';
		}
		return $string;
	}

	static function set_wish_list_to_session_and_member($array) {
		//make sure it is an array
		if(!is_array($array)) {
			user_error("There is an error in storing your wish list, your variable should be an array", E_USER_WARNING);
		}
		else {
			//set session variable
			Session::clear(self::get_session_variable_name()."_data");
			Session::save();
			//Session::set(self::get_session_variable_name()."_data", null);
			//Session::save();
			Session::set(self::get_session_variable_name()."_data", serialize($array));
			Session::save();
			self::set_wish_list_to_member($array);
		}
	}

	static function set_wish_list_to_member($array) {
		$member = Member::currentMember();
		if($member) {
			$member->WishList = serialize($array);
			$member->write();
			return true;
		}
		return false;
	}

	static function set_inline_requirements() {
		if(!Director::is_ajax() && !self::$requirements_added) {
			self::$requirements_added = true;
			$wishListPage = DataObject::get_one("WishListPage");
			Requirements::javascript("wishlist/javascript/WishList.js");
			Requirements::themedCSS("WishList");
			if($wishListPage) {
				$confirmDeleteText = $wishListPage->RemovedFromListConfirmation;
				Requirements::customScript("WishList.set_confirm_delete_text('".Convert::raw2js($confirmDeleteText)."')", "set_confirm_delete_text");
				$confirmRetrieveText = $wishListPage->RetrieveListConfirmation;
				Requirements::customScript("WishList.set_confirm_retrieve_text('".Convert::raw2js($confirmRetrieveText)."')", "set_confirm_retrieve_text");
				Requirements::customScript("WishList.set_reload_list_url('".Convert::raw2js(Director::absoluteURL($wishListPage->Link()."loadlist/"))."')", "set_reload_list_url");
			}
		}
	}

	// ____ actions

	function addtowishlist() {
		$id = $this->getIDForWishList();
		$outcome = false;
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$outcome = true;
				$array = self::get_wish_list_from_session_array();
				$array[$id]= $id;
				self::set_wish_list_to_session_and_member($array);
			}
		}
		return $this->standardReturn($outcome, "AddedToListText", "AddedToListTextError", "WishListLinkInner");
	}

	function removefromwishlist() {
		$id = $this->getIDForWishList();
		$outcome = false;
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$outcome = true;
				//get current wish list
				$array = self::get_wish_list_from_session_array();
				//remove from wish list
				unset($array[$id]);
				//reset
				self::set_wish_list_to_session_and_member($array);
			}
		}
		return $this->standardReturn($outcome, "RemovedFromListText", "RemovedFromListTextError", "WishListLinkInner");
	}

	function savewishlist() {
		$outcome = self::set_wish_list_to_member(self::get_wish_list_from_session_array());
		return $this->standardReturn($outcome, "SavedWishListText", "SavedWishListTextError", "WishListSaveAndRetrieveInner");
	}

	function retrievewishlist() {
		$outcome = false;
		self::set_wish_list_to_session_and_member(self::get_wish_list_from_member_array());
		$outcome = true;
		return $this->standardReturn($outcome, "RetrievedWishListText", "RetrievedWishListTextError", "WishListListInner");
	}

	function loadlist() {
		if(Director::is_ajax()) {
			return $this->owner->renderWith("WishListLinkInner");
		}
	}

	function clearwishlist() {
		$newArray = array();
		self::set_wish_list_to_session_and_member($newArray);
		return $this->standardReturn(true, "ClearWishList", "", "WishListSaveAndRetrieveInner");
	}

	// ____ template variables

	function AddWishListRequirements() {
		self::set_inline_requirements();
	}

	function WishList() {
		$array = self::get_wish_list_from_session_array();
		if(is_array($array) && count($array) ) {
			$stage = Versioned::current_stage();
			$baseClass = "SiteTree";
			$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
			return DataObject::get("$baseClass", "$stageTable.ID IN (".implode(",", $array).")");
		}
		return null;
	}

	function WishListMessage() {
		//retrieve message
		$msg = Session::get(self::get_session_variable_name()."_message");
		//remove it from session
		Session::set(self::get_session_variable_name()."_message", null);
		//return it
		return $msg;
	}


	function NumberOfItemsInSavedOnes() {
		$array = self::get_wish_list_from_member_array();
		if(is_array($array) && ($count = count($array))) {
			return $count;
		}
	}

	function NumberOfItemsInSessionOnes() {
		$array = self::get_wish_list_from_session_array();
		if(is_array($array) && ($count = count($array))) {
			return $count;
		}
	}

	// ____ internal functions

	protected function getVariableFromwishListPage ($variableName) {return $this->wishListPage()->$variableName;}

	protected function getIDForWishList() {
		//check URL Param
		//$id = intval(Director::URLParam("ID"));
		//if(!$id) {
		return $this->owner->ID;
		//}
		//return $id;
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
		else {
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


}
