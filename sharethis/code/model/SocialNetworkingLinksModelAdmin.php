<?php
/**
 *
 *
 **/

class SocialNetworkingLinksModelAdmin extends ModelAdmin {

	public static $managed_models = array("SocialNetworkingLinksDataObject");

	public static $url_segment = 'social';

	public static $menu_title = 'Social Link';

}