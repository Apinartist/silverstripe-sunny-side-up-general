<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 **/

class EcommerceModifiersModelAdmin extends ModelAdmin {

	public static $managed_models = array("PickUpOrDeliveryModifierOptions", "GSTTaxModifierOptions");

	public static function set_managed_models(array $array) {
		self::$managed_models = $array;
	}
	public static $url_segment = 'shopoptions';

	public static $menu_title = 'Shop Options';

}