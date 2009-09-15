<?php


class ShoppingCartExtension extends ShoppingCart {

}

class ShoppingCartExtension_Controller extends ShoppingCart_Controller {

	static function modifier_json_code() {
		return parent::json_code();
	}

}