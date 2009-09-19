<?php

class WebPortfolioAdmin extends ModelAdmin {

	public static $managed_models = array(
		'WebPortfolioAgent',
		'WebPortfolioWhatWeDidDescriptor',
		'WebPortfolioItem'
	);

	static $url_segment = 'webportfolio'; // will be linked as /admin/products

	static $menu_title = 'Web Portfolio';

}