<?php

/**
* @author Nicolaas Francken
*
*
*
**/

class SiteWideSettings extends ModelAdmin {

	public static $managed_models = array("SiteWideSettings", "SiteTree");

	public static $url_segment = 'sitewidesettings';

	public static $menu_title = 'Settings';


}