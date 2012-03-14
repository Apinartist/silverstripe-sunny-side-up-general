<?php

class WishListDecorator_DataObject extends DataObjectDecorator {

	/**
	 * Return where this page is on the current members wishlist.
	 * This decoration probably should be added to Product and not SiteTree (as far as I can tell).
	 * @return boolean
	 */
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

	/**
	 * Provides a list of allowed methods that can be called via RESTful api.
	 */
	static $allowed_actions = array(
		"addtowishlist" => true,
		"removefromwishlist" => true,
		"clearwishlist" => true,
		"savewishlist" => true,
		"retrievewishlist" => true,
		"loadlist" => true
	);

	/**
	 * Whether requirements have already been added to the current page.
	 */
	private static $requirements_added = false;

	/**
	 * Name of session variable for storing wishlist data.
	 */
	protected static $session_variable_name = "WishListDecoratorArray";
	/**
	 * Set the name of the session variable, to change from default.
	 * @param string
	 */
	static function set_session_variable_name($v){
		self::$session_variable_name = $v;
	}
	/**
	 * Return the name of the session variable.
	 * @return string
	 */
	static function get_session_variable_name () {
		return self::$session_variable_name;
	}

	/**
	 * Return wish list data from session as an array.
	 * @return array
	 */
	public static function get_wish_list_from_session_array() {
		//store in static variable so that you do not have to retrieve all the time...
		$string = self::get_wish_list_from_session_serialized();
		//set static variable
		return unserialize($string);
	}

	/**
	 * Return wish list data from session as a serialised array.
	 * @return string (serialised array)
	 */
	public static function get_wish_list_from_session_serialized() {
		$string = Session::get(self::get_session_variable_name()."_data");
		if(!is_string($string)) {
			$string = '';
		}
		if(!$string) {
			$string = self::get_wish_list_from_member_serialized();
			if($string) {
				$array = unserialize($string);
				if(is_array($array) && count($array)) {
					self::set_wish_list_to_session_and_member($array);
				}
			}
		}
		return $string;
	}

	/**
	 * Return wish list data from current member as an array.
	 * @return array
	 */
	public static function get_wish_list_from_member_array() {
		//store in static variable so that you do not have to retrieve all the time...
		$string = self::get_wish_list_from_member_serialized();
		//set static variable
		return unserialize($string);
	}

	/**
	 * Return wish list data from current member as a serialised array.
	 * @return string (serialised array)
	 */
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

	/**
	 * Save wish list data to session and current member.
	 * @param array
	 */
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

	/**
	 * Save wish list data to current member.
	 * @param array
	 */
	static function set_wish_list_to_member($array) {
		$member = Member::currentMember();
		if($member) {
			$member->WishList = serialize($array);
			$member->write();
			return true;
		}
		return false;
	}

	/**
	 * Add js and css requirements to current page.
	 * Can be called more than once and will only add requirements once.
	 */
	static function set_inline_requirements() {
		if(!Director::is_ajax() && !self::$requirements_added) {
			self::$requirements_added = true;
			$wishListPage = DataObject::get_one("WishListPage");
			Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
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

	/**
	 * Add the current page to the wishlist.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
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

	/**
	 * Remove the current page to the wishlist.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
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

	/**
	 * Save the wishlist from the session to the current member.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function savewishlist() {
		$outcome = self::set_wish_list_to_member(self::get_wish_list_from_session_array());
		return $this->standardReturn($outcome, "SavedWishListText", "SavedWishListTextError", "WishListSaveAndRetrieveInner");
	}

	/**
	 * Retrieve the wishlist from the current member and save to the session.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function retrievewishlist() {
		$outcome = false;
		self::set_wish_list_to_session_and_member(self::get_wish_list_from_member_array());
		$outcome = true;
		return $this->standardReturn($outcome, "RetrievedWishListText", "RetrievedWishListTextError", "WishListListInner");
	}

	/**
	 * If this is ajax then return the wishlist html otherwise do nothing.
	 * @return string | null
	 */
	function loadlist() {
		if(Director::is_ajax()) {
			return $this->owner->renderWith("WishListLinkInner");
		}
	}

	/**
	 * Set the wishlist to empty for the current member and in the session.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function clearwishlist() {
		$newArray = array();
		self::set_wish_list_to_session_and_member($newArray);
		return $this->standardReturn(true, "ClearWishList", "", "WishListSaveAndRetrieveInner");
	}

	// ____ template variables

	/**
	 * Add js and css requirements.
	 */
	function AddWishListRequirements() {
		self::set_inline_requirements();
	}

	/**
	 * Retrieve wishlist entries from session and return.
	 * @return DataObjectSet | null
	 */
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

	/**
	 * Retrieve wish list message from session.
	 * @return string
	 */
	function WishListMessage() {
		//retrieve message
		$msg = Session::get(self::get_session_variable_name()."_message");
		//remove it from session
		Session::set(self::get_session_variable_name()."_message", null);
		//return it
		return $msg;
	}

	/**
	 * Return number of items in member wish list.
	 * @return int | null
	 */
	function NumberOfItemsInSavedOnes() {
		$array = self::get_wish_list_from_member_array();
		if(is_array($array) && ($count = count($array))) {
			return $count;
		}
	}

	/**
	 * Return number of items in session wish list.
	 * @return int | null
	 */
	function NumberOfItemsInSessionOnes() {
		$array = self::get_wish_list_from_session_array();
		if(is_array($array) && ($count = count($array))) {
			return $count;
		}
	}

	// ____ internal functions

	/**
	 * Retrieve variable from wish list page.
	 * @return mixed Depends on what $VariableName is.
	 */
	protected function getVariableFromwishListPage ($variableName) {
		return $this->wishListPage()->$variableName;
	}

	/**
	 * Return the ID to be stored in the wish list of the current page.
	 * @return int
	 */
	protected function getIDForWishList() {
		//check URL Param
		//$id = intval(Director::URLParam("ID"));
		//if(!$id) {
		return $this->owner->ID;
		//}
		//return $id;
	}

	/**
	 * Retrieve a single wish list page.
	 * @return WishListPage
	 */
	protected function wishListPage() {
		return DataObject::get_one("wishListPage");
	}

	/**
	 * Return template html if this is an ajax call otherwise redirect back.
	 * Set session message based on $outcome.
	 * @param boolean $outcome Whether the current action was successful.
	 * @param string $successMessageName Name of success message variable, to be retrieved from WishListPage.
	 * @param string $errorMessageName Name of error message variable, to be retrieved from WishListPage.
	 * @param string $template Name of template to render if this an ajax call.
	 * @return string | null
	 */
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
