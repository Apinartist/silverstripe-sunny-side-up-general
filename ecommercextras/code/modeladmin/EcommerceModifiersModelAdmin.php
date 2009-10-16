<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 **/

class EcommerceModifiersModelAdmin extends ModelAdmin {

	public static $managed_models = array("PickUpOrDeliveryModifierData");

	public static function set_managed_models(array $array) {
		self::$managed_models = $array;
	}
	public static $url_segment = 'orderoptions';

	public static $menu_title = 'Orders';

}