<?php
/**
 *@author: nicolaas[at]sunnysideup.co.nz
 *@description: manages social networking data objects
 **/

class SocialNetworkingLinksModelAdmin extends ModelAdmin {

	public static $managed_models = array("SocialNetworkingLinksDataObject", "ShareThisDataObject");

	public static $url_segment = 'social';

	public static $menu_title = 'Social Media';

}