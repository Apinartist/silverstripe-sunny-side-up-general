<?php



class EcommerceWishListDecorator extends Extension {

	static $allowed_actions = array(
		"addtowishlist" => true,
		"removefromwishlist" => true,
		"clearwishlist" => true,
		"savewishlist" => true,
		"retrievewishlist" => true
	);

	function getAddedToListText () {return $this->EcommerceWishListPage()->AddedToListText;}
	function getCouldNotAddedToListText () {return $this->EcommerceWishListPage()->CouldNotAddedToListText;}
	function getRemovedFromListConfirmation () {return $this->EcommerceWishListPage()->RemovedFromListConfirmation;}
	function getRemovedFromListText () {return $this->EcommerceWishListPage()->RemovedFromListText ;}
	function getCouldNotRemovedFromListText () {return $this->EcommerceWishListPage()->CouldNotRemovedFromListText;}
	function getClearWishList () {return $this->EcommerceWishListPage()->ClearWishList;}
	function getSavedWishListText () {return $this->EcommerceWishListPage()->SavedWishListText;}
	function getSavedErrorWishListText () {return $this->EcommerceWishListPage()->SavedErrorWishListText;}
	function getRetrievedWishListText () {return $this->EcommerceWishListPage()->RetrievedWishListText;}
	function getRetrievedErrorWishListText () {return $this->EcommerceWishListPage()->RetrievedErrorWishListText;}

	protected static $session_variable_name = "EcommerceWishListDecoratorArray";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	protected static $wish_list_array = array();
		protected static function set_wish_list_array($v){self::$wish_list_array = $v;}
		protected static function get_wish_list_array () {return self::$wish_list_array;}

	function IsOnWishList() {
		Requirements::javascript("ecommerce_wishlist/javascript/EcommerceWishList.js");
		$msg = $this->getRemovedFromListConfirmation();
		Requirements::customScript("EcommerceWishList.set_ConfirmDeleteText('".Convert::raw2js($msg)."')");
		$array = Session::get(self::get_session_variable_name());
		if(isset($array[$this->owner->ID])) {
			return true;
		}
		else {
			return false;
		}
	}

	function WishList() {
		$array = Session::get(self::get_session_variable_name());
		$stage = Versioned::current_stage();
		$baseClass = "SiteTree";
		$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
		return DataObject::get("$baseClass", "$stageTable.ID IN (".implode(",", $array).")");
	}

	function CanSaveWishList() {
		return $this->WishList() && Member::currentMember();
	}

	function CanRetrieveWishList() {
		if($member = Member::currentMember()) {
			return $member->EcommerceWishList;
		}
	}

	function WishListMessage() {
		//retrieve message
		$msg = Session::get(self::get_session_variable_name()."_message");
		//remove it from session
		Session::set(self::get_session_variable_name()."_message", null);
		//return it
		return $msg;
	}

	function addtowishlist() {
		$id = $this->getIDForWishList();
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$array = $this->getWishListArray();
				$array[$id]= $id;
				$this->setWishListArray($array);
				if(Director::is_ajax()) {
					return $this->getAddedToListText();
				}
				else {
					Session::set(self::get_session_variable_name()."_message", $this->getAddedToListText());
					Director::redirectBack();
					return;
				}
			}
		}
		if(Director::is_ajax()) {
			return $this->getCouldNotAddedToListText;
		}
		else {
			Session::set(self::get_session_variable_name()."_message", $this->getCouldNotAddedToListText());
			Director::redirectBack();
			return;
		}
	}

	function removefromwishlist() {
		$id = $this->getIDForWishList();
		$error = '';
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				//get current wish list
				$array = $this->getWishListArray();
				//remove from wish list
				unset($array[$id]);
				//reset
				$this->setWishListArray($array);
				if(Director::is_ajax()) {
					return $this->getRemovedFromListText();
				}
				else {
					Session::set(self::get_session_variable_name()."_message", $this->getRemovedFromListText());
					Director::redirectBack();
					return;
				}

			}
			else {
				$error .= "could not find page. ";
			}
		}
		else {
			$error .= "could not find id. ";
		}
		//soemthing did not work...
		if(Director::is_ajax()) {
			return $this->getCouldNotRemovedFromListText().": ".$error;
		}
		else {
			Session::set(self::get_session_variable_name()."_message", $this->getCouldNotRemovedFromListText().": ".$error);
			Director::redirectBack();
			return;
		}
	}

	function savewishlist() {
		if($this->CanSaveWishList()) {
			$member = Member::currentMember();
			$member->EcommerceWishList = serialize($this->getWishListArray());
			$member->write();
			if(Director::is_ajax()) {
				return $this->getSavedWishListText();
			}
			else {
				Session::set(self::get_session_variable_name()."_message", $this->getSavedWishListText());
				Session::save();
				Director::redirectBack();
				return;
			}
		}
		if(Director::is_ajax()) {
			return $this->getSavedErrorWishListText();
		}
		else {
			Session::set(self::get_session_variable_name()."_message", $this->getSavedErrorWishListText());
			Session::save();
			Director::redirectBack();
			return;
		}
	}
	function retrievewishlist() {
		if($this->CanRetrieveWishList()) {
			$member = Member::currentMember();
			$this->setWishListArray(unserialize($member->EcommerceWishList));
			if(Director::is_ajax()) {
				return $this->getRetrievedWishListText();
			}
			else {
				Session::set(self::get_session_variable_name()."_message", $this->getRetrievedWishListText());
				Director::redirectBack();
				return;
			}
		}
		if(Director::is_ajax()) {
			return $this->getRetrievedErrorWishListText();
		}
		else {
			Session::set(self::get_session_variable_name()."_message", $this->getRetrievedErrorWishListText());
			Director::redirectBack();
			return;
		}
	}

	function clearwishlist() {
		Session::clear(self::get_session_variable_name());
		Session::save();
		if(Director::is_ajax()) {
			return $this->getClearWishList();
		}
		else {
			Session::set(self::get_session_variable_name()."_message", $this->getClearWishList());
			Director::redirectBack();
			return;
		}
	}

	protected function getIDForWishList() {
		//check URL Param
		$id = intval(Director::URLParam("ID"));
		if(!$id) {
			$id = $this->owner->ID;
		}
		return $id;
	}

	protected function getWishListArray() {
		//store in static variable so that you do not have to retrieve all the time...
		if(!is_array(self::get_wish_list_array())) {
			$array = Session::get(self::get_session_variable_name());
			//if it does not exist, then we set it and retrieve it again...
			if(!is_array($array)) {
				$this->setWishListArray(array());
				$array = Session::get(self::get_session_variable_name());
			}
			//set static variable
			self::set_wish_list_array(($array));
		}
		//return static variable
		return self::get_wish_list_array();
	}

	protected function setWishListArray($array) {
		//make sure it is an array
		if(!is_array($array)) {
			user_error("There is an error in storing your wish list, your variable should be an array", E_USER_WARNING);
		}
		else {
			//set session variable
			Session::clear(self::get_session_variable_name());
			Session::set(self::get_session_variable_name(), null);
			Session::set(self::get_session_variable_name(), "clear");
			Session::set(self::get_session_variable_name(), array());
			Session::set(self::get_session_variable_name(), $array);
			Session::save();
		}
	}

	protected function EcommerceWishListPage() {
		return DataObject::get_one("EcommerceWishListPage");
	}

}