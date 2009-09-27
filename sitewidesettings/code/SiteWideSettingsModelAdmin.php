<?php

/**
 * @author Nicolaas Francken
 *
 *
 *
 **/

class SiteWideSettingsModelAdmin extends ModelAdmin {

	public static $managed_models = array(
		"SiteWideSettings"
	);

	public static $url_segment = 'sws';

	public static $menu_title = 'Settings';


}