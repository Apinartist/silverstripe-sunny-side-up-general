<?php



class EcommerceWishListDecorator extends Extension {

	protected static $session_variable_name = "EcommerceWishListDecoratorArray";
		static function set_session_variable_name($v){self::$session_variable_name = $v;}
		static function get_session_variable_name () {return self::$session_variable_name;}

	function WishList() {
		$array = Session::get(self::get_session_variable_name());
		$stage = Versioned::current_stage();
		$baseClass = "SiteTree";
		$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
		return DataObject::get("$stageTable", "$stageTable.ID IN (".implode(",", $array).")");
	}


	function addtowishlist() {
		$id = intval(Director::URLParam("ID"));
		if($page = Director::get_by_id("SiteTree", $id)) {
			$array = Session::get(self::get_session_variable_name());
			$array[$id]= $id;
			Session::set(self::get_session_variable_name(), null);
			Session::set(self::get_session_variable_name(), $array);
		}
		Director::redirectBack();
	}

	function removefromwishlist() {
		$id = intval(Director::URLParam("ID"));
		if($page = Director::get_by_id("SiteTree", $id)) {
			$array = Session::get(self::get_session_variable_name());
			unset($array[$id]);
			Session::set(self::get_session_variable_name(), null);
			Session::set(self::get_session_variable_name(), $array);
		}
		Director::redirectBack();
	}


}