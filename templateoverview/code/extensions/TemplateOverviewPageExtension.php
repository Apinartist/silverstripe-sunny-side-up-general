<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: adds functionality to controller for dev purposes only
 *
 **/


class TemplateOverviewPageExtension extends Extension {

	public function TemplateOverviewPage() {
		return DataObject::get_one("TemplateOverviewPage");
	}

	public function IncludeTemplateOverviewDevelopmentFooter() {
		if(Director::isDev()) {
			if($m = Member::currentUser()) {
				if($m->isAdmin()) {
					Requirements::javascript("templateoverview/javascript/TemplateOverviewExtension.js");
					return true;
				}
			}
		}
		return false;
	}


}