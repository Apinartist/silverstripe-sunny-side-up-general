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

}