<?php



class EcommerceWishListDecorator extends Extension {

	protected static $session_variable_name = "EcommerceWishListDecoratorArray";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	function IsOnWishList() {
		$id = $this->getIDForWishList();
		if($id) {
			$array = Session::get(self::get_session_variable_name());
			return isset($array[$id]);
		}
		return false;
	}

	function WishList() {
		$array = Session::get(self::get_session_variable_name());
		$stage = Versioned::current_stage();
		$baseClass = "SiteTree";
		$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
		return DataObject::get("$stageTable", "$stageTable.ID IN (".implode(",", $array).")");
	}

	function addtowishlist() {
		$id = $this->getIDForWishList();
		if($id) {
			if($page = Director::get_by_id("SiteTree", $id)) {
				$array = $this->getWishListArray();
				$array[$id]= $id;
				$this->setWishListArray($array);
				if(Director::is_ajax()) {
					return "added to wish list";
				}
				else {
					Director::redirectBack();
				}
			}
		}
		if(Director::is_ajax()) {
			return "could not add to wish list";
		}
		else {
			Director::redirectBack();
		}
	}

	function removefromwishlist() {
		$id = $this->getIDForWishList();
		if($id) {
			if($page = Director::get_by_id("SiteTree", $id)) {
				$array = $this->getWishListArray();
				if(isset($array[$id])) {
					unset($array[$id]);
					$this->setWishListArray($array);
					if(Director::is_ajax()) {
						return "removed from wish list";
					}
					else {
						Director::redirectBack();
					}
				}
			}
		}
		if(Director::is_ajax()) {
			return "could not remove from wish list";
		}
		else {
			Director::redirectBack();
		}
	}


	protected function getIDForWishList() {
		$id = intval(Director::URLParam("ID"));
		if(!$id) {
			$id = $this->owner->ID;
		}
		return $id;
	}

	protected function getWishListArray() {
		if(!is_array(self::$wish_list_array)) {
			$array = unserialize(Session::get(self::get_session_variable_name()));
			if(!is_array($array)) {
				$this->setWishListArray(array());
				return array();
			}
			else {
				return $array;
			}
		}
	}

	protected function setWishListArray($array) {
		if(!is_array($array)) {
			user_error("There is an error in storing your wish list, your variable should be an array", E_USER_NOTICE);
		}
		else {
			Session::set(self::get_session_variable_name(), null);
			Session::set(self::get_session_variable_name(), serialize($array));
		}
	}

}