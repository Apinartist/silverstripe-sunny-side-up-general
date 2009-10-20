<?php

class ExtendedProductVariationAdmin extends ModelAdmin {

	public static $managed_models = array(
		'ExtendedProductVariation1',
		'ExtendedProductVariation2',
		'ExtendedProductVariation3',
		'ExtendedProductVariation4',
		'ExtendedProductVariation5'
	);

	static $url_segment = 'productvariations'; // will be linked as /admin/products

	static $menu_title = 'Product Variations';

}