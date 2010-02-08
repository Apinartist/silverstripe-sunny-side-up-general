<?php

class StandingOrderAdmin extends ModelAdmin {

	public static $managed_models = array(
		'StandingOrder'
	);
	
	public static $url_segment = 'standing-orders';

	public static $menu_title = 'Standing Orders';

}