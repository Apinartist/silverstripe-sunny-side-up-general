<?php



class EcommerceWishListDecorator extends Extension {

	static $allowed_actions = array(
		"addtowishlist" => true,
		"removefromwishlist" => true,
		"clearwishlist" => true
	);

	protected static $added_to_list_text = "added to wish list";
		static function set_added_to_list_text($v){self::$added_to_list_text = $v;}
		static function get_added_to_list_text () {return self::$added_to_list_text;}

	protected static $could_not_added_to_list_text = "could not add to wish list";
		static function set_could_not_added_to_list_text($v){self::$could_not_added_to_list_text = $v;}
		static function get_could_not_added_to_list_text () {return self::$could_not_added_to_list_text;}

	protected static $removed_from_list_text = "removed from wish list";
		static function set_removed_from_list_text($v){self::$removed_from_list_text = $v;}
		static function get_removed_from_list_text () {return self::$removed_from_list_text;}

	protected static $could_not_removed_from_list_text = "could not be removed from wish list";
		static function set_could_not_removed_from_list_text($v){self::$could_not_removed_from_list_text = $v;}
		static function get_could_not_removed_from_list_text () {return self::$could_not_removed_from_list_text;}

	protected static $clear_wish_list = "cleared wish list";
		static function set_clear_wish_list($v){self::$clear_wish_list = $v;}
		static function get_clear_wish_list () {return self::$clear_wish_list;}

	protected static $session_variable_name = "EcommerceWishListDecoratorArray";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	protected static $wish_list_array = array();
		static function set_wish_list_array($v){self::$wish_list_array = $v;}
		static function get_wish_list_array () {return self::$wish_list_array;}

	function IsOnWishList() {
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
					return self::get_added_to_list_text();
				}
				else {
					Session::set(self::get_session_variable_name()."_message", self::get_added_to_list_text());
					Director::redirectBack();
					return;
				}
			}
		}
		if(Director::is_ajax()) {
			return self::get_could_not_added_to_list_text();
		}
		else {
			Session::set(self::get_session_variable_name()."_message", self::get_could_not_added_to_list_text());
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
					return self::get_removed_from_list_text();
				}
				else {
					Session::set(self::get_session_variable_name()."_message", self::get_removed_from_list_text());
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
			return self::get_could_not_removed_from_list_text().": ".$error;
		}
		else {
			Session::set(self::get_session_variable_name()."_message", self::get_could_not_removed_from_list_text().": ".$error);
			Director::redirectBack();
			return;
		}
	}

	function savewishlist() {

	}

	function clearwishlist() {
		Session::clear(self::get_session_variable_name());
		if(Director::is_ajax()) {
			return self::get_clear_wish_list();
		}
		else {
			Session::set(self::get_session_variable_name()."_message", self::get_clear_wish_list());
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
			Session::set(self::get_session_variable_name(), "");
			Session::set(self::get_session_variable_name(), array());
			Session::set(self::get_session_variable_name(), $array);
		}
	}


}