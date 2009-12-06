<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 * manages the default options for product variations
 */

class ExtendedProductVariationAdmin extends ModelAdmin {

	public static $managed_models = array(
		'ExtendedProductVariationGroup',
		'ExtendedProductVariationOption'
	);

	static $url_segment = 'productvariations'; // will be linked as /admin/products

	static $menu_title = 'Product Variations';

}