<?php
class ProductsAndGroupsModelAdmin extends ModelAdmin {

	public static $record_controller_class = "ModelAdminRecordControllerForSiteTreeObjects";

	public static function set_managed_models($array) {
		self::$managed_models = $array;
	}

	public static $url_segment = 'ecommerce';

	public static $menu_title = 'Ecommerce';

}