<?php

class WishListDecorator_DataObject extends DataObjectDecorator {

	/**
	 * Return whether this DataObject is on the current members wishlist.
	 * @return boolean
	 */
	function IsOnWishList($object = NULL) {
		if(!$object){
			$object = $this->owner;
		}
		$array = WishListDecorator_Controller::get_wish_list_from_member_array();
		$dataobject_index = $this->owner->WishListIndex("string");
		return isset($array[$dataobject_index]);
	}

	/**
	 * Add js and css requirements.
	 * Defined here rather than in WishListDecorator_Controller so that it can used with a list
	 * of dataobjects and not just on a single page.
	 */
	function AddWishListRequirements() {
		WishListDecorator_Controller::set_inline_requirements();
	}

	/**
	 *
	 * @return String
	 */
	function WishlistIndex($type){
		if($type == "string") {
			return $object->owner->ClassName.".".$object->owner->ID;
		}
		elseif($type == "array") {
			return array(
				0 => $this->owner->ClassName,
				1 => $this->owner->ID
			);
		}
		else {
			user_error("Type needs to be 'string' or 'array', you set it to: ".$string);
		}
	}

}

class WishListDecorator_Controller extends Extension {

	// ____ statics

	/**
	 * Provides a list of allowed methods that can be called via RESTful api.
	 *
	 * Use an array of string instead of an array associative array of strings to booleans.
	 * The latter causes an infinite redirect in come cases, no exactly sure why but using
	 * an array of strings seems to fix the problem.
	 */
	static $allowed_actions = array(
		"addtowishlist",
		"removefromwishlist",
		"clearwishlist",
		"savewishlist",
		"retrievewishlist",
		"loadlist",
		"wishlistdebug",
	);

	/**
	 * Whether requirements have already been added to the current page.
	 */
	private static $requirements_added = false;

	/**
	 * Name of session variable for storing wishlist message.
	 */
	protected static $session_variable_name = "WishListDecoratorMessage";

	protected static $data = null;

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

	static function get_member_for_wishlist(){
		$member = Member::currentMember();
		if(!$member) {
			$wishListMemberID = Session::get(self::get_session_variable_name()."_wishListMemberID");
			if($wishListMemberID) {
				if($wishListMember = DataObject::get_by_id("WishListMember", intval($wishListMemberID))) {
					//do nothing
				}
				else {
					$wishListMemberID = 0;
				}
			}
			if(!$wishListMemberID) {
				$wishListMember = new WishListMember();
				$wishListMember->write();
				Session::set(self::get_session_variable_name()."_wishListMemberID", $wishListMember->ID);
			}
			return $wishListMember;
		}
		else {
			//copy if the member does not have a wish list, but the session does.
			if(!$member->WishList) {
				if(!$member->IsAdmin()) {
					$wishListMemberID = Session::get(self::get_session_variable_name()."_memberID");
					if($wishListMemberID) {
						if($wishListMember = DataObject::get_by_id("WishListMember", intval($wishListMemberID))) {
							if($wishListMember->WishList) {
								$member->WishList = $wishListMember->WishList;
								$member->write();
							}
							$wishListMember->delete();
							Session::clear(self::get_session_variable_name()."_memberID");
						}
					}
				}
			}
			return $member;
		}
	}

	/**
	 * Return wish list data from current member as an array.
	 * @return array
	 */
	public static function get_wish_list_from_member_array() {
		$string = self::get_wish_list_from_member_serialized();
		return unserialize($string);
	}

	/**
	 * Return wish list data from current member as a serialised array.
	 * @return string (serialised array)
	 */
	public static function get_wish_list_from_member_serialized() {
		$member = self::get_member_for_wishlist();
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
	 * Save wish list data to current member.
	 * @param array
	 * @return Boolean
	 */
	static function set_wish_list_to_member($array, $force = false) {
		if(!is_array($array)) {
			user_error("There is an error in storing your wish list, your variable should be an array", E_USER_WARNING);
		}
		$member = self::get_member_for_wishlist();
		if($member) {
			$newValue = serialize($array);
			if($member->WishList != $newValue || $force) {
				$member->WishList = $newValue;
				$member->write();
			}
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
		$idAndClass = $this->getIDForWishList();
		$outcome = false;
		$object = NULL;

		if($idAndClass) {
			if($object = self::get_wish_list_object($idAndClass)){
				$outcome = true;
				$array = self::get_wish_list_from_member_array();
				$array[$object->WishListIndex("string")] = $this->WishListIndex("array");
				self::set_wish_list_to_member($array, true);
			}
		}
		return $this->standardReturn($outcome, "AddedToListText", "AddedToListTextError", "WishListLinkInner", $object);
	}

	/**
	 * Remove the current page to the wishlist.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function removefromwishlist() {
		$idAndClass = $this->getIDForWishList();
		$outcome = false;
		$object = NULL;
		if($idAndClass) {
			if($object = self::get_wish_list_object($idAndClass)){
				$outcome = true;
				//get current wish list
				$array = self::get_wish_list_from_member_array();
				//remove from wish list
				$dataobject_index = $object->WishListIndex("string");
				if(isset($array[$dataobject_index])) {
					unset($array[$dataobject_index]);
				}
				//reset
				self::set_wish_list_to_member($array, true);
			}
		}
		return $this->standardReturn($outcome, "RemovedFromListText", "RemovedFromListTextError", "WishListLinkInner", $object);
	}

	/**
	 * Save the wishlist to the current member.
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function savewishlist() {
		return $this->standardReturn($outcome, "SavedWishListText", "SavedWishListTextError", "WishListSaveAndRetrieveInner");
	}

	/**
	 * Retrieve the wishlist from the current member
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function retrievewishlist() {
		return $this->standardReturn(true, "RetrievedWishListText", "RetrievedWishListTextError", "WishListListInner");
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
	 * Set the wishlist to empty for the current member
	 * Returns html if this is ajax otherwise redirects back.
	 * @return string | null
	 */
	function clearwishlist() {
		$newArray = array();
		self::set_wish_list_to_member($newArray, true);
		return $this->standardReturn(true, "ClearWishList", "", "WishListSaveAndRetrieveInner");
	}

	// ____ template variables


	/**
	 * Retrieve wishlist entries from session and return.
	 * @return DataObjectSet | null
	 */
	function WishList() {
		if(self::$data === null) {
			self::$data  = false;
			$array = self::get_wish_list_from_member_array();
			if(is_array($array) && count($array) ) {
				$stage = Versioned::current_stage();
				$objects = array();
				foreach($array as $value){
					if($object = self::get_wish_list_object($value)) {
						$objects[] = $object;
					}
				}
				if(count($objects)) {
					self::$data = new DataObjectSet($objects);
				}
			}
		}
		return self::$data;
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
	 * Return number of items in wish list.
	 * @return int | null
	 */
	function NumberOfItemsInSessionOnes() {
		$array = self::get_wish_list_from_member_array();
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
	 * Return the ID and Class to be stored in the wish list of the current page.
	 * @return Array
	 */
	protected function getIDForWishList() {
		if(isset($_GET['id']) && $_GET['id'] && isset($_GET['class']) && $_GET['class']){
			return array(Convert::raw2sql($_GET['class']), intval($_GET['id']));
		}
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
	protected function standardReturn($outcome, $successMessageName, $errorMessageName, $template, $object = NULL) {
		$template_object = $object ? $object : $this->owner;
		if($outcome) {
			Session::set(self::get_session_variable_name()."_message", $this->getVariableFromwishListPage($successMessageName));
			if(Director::is_ajax()) {
				return $template_object->renderWith($template);
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
				return $template_object->renderWith($template);
			}
			else {
				Session::save();
				Director::redirectBack();
				return;
			}
		}
	}

	/**
	 * Instantiate a DataObject base on the id-classname pair passed.
	 * Checks that classname is valid and is a DataObject.
	 * Returns NULL is not valid or doesn't exist.
	 * @param array(id, classname)
	 * @return DataObject | null
	 */
	protected static function get_wish_list_object($array){
		if(is_array($array)){
			$class = $array[0];
			$id = intval($array[1]);
			if(class_exists($class) && $id){
				return DataObject::get_by_id($class, $id);
			}
		}
		return NULL;
	}

 function wishlistdebug(){
		print_r(self::get_wish_list_from_member_array());
 }


}
