<?php

class WebQuoteAdmin extends ModelAdmin {

	public static $managed_models = array(
		'WebQuoteCurrencyConversion',
		'WebQuoteDesignAspect',
		'WebQuoteOption',
		'WebQuoteSalesGroup',
		'WebQuoteSalesItem',
		'WebQuoteTemplate',
		'WebQuoteTimeframeOption',
		'WebQuoteWeDeliverYouDeliver'
	);

	static $url_segment = 'webquote'; // will be linked as /admin/products

	static $menu_title = 'Web Quote';

}