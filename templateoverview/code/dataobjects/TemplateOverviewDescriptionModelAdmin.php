<?php
/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description model admin for template overview
 **/


class TemplateOverviewDescriptionModelAdmin extends ModelAdmin {

	public static $managed_models = array("TemplateOverviewDescription");

	public static $url_segment = 'templates';

	public static $menu_title = 'templates';

}