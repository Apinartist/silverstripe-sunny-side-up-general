<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: adds functionality to controller for dev purposes only
 *
 **/


class TemplateOverviewPage_ControllerExtension extends Extension {

	public function TemplateOverviewPage() {
		return DataObject::get_one("TemplateOverviewPage");
	}

	public function IncludeTemplateOverviewDevelopmentFooter() {
		if(Director::isDev()) {
			Requirements::javascript("templateoverview/javascript/TemplateOverviewExtension.js");
			return true;
		}
		return false;
	}


}