<?php
class ProductsAndGroupsModelAdmin extends ModelAdmin {

	public static $record_controller_class = "ModelAdminRecordControllerForSiteTreeObjects";

	static function set_managed_modules($array) {
		self::$managed_models = $array;
	}

	public static $url_segment = 'ecommerce';

	public static $menu_title = 'Ecommerce';

}