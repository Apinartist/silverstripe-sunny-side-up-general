<?php
class ProductsAndGroupsModelAdmin extends ModelAdmin {

	public static $record_controller_class = "ModelAdminRecordControllerForSiteTreeObjects";

	public static $managed_models = array("Product", "ProductGroup");

	public static function set_managed_models(array $array) {
		self::$managed_models = $array;
	}

	public static $url_segment = 'products';

	public static $menu_title = 'Products';

}