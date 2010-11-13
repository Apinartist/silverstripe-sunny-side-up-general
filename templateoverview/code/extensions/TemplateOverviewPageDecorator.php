<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: adds functionality to controller for dev purposes only
 *
 **/


class TemplateOverviewPageDecorator extends DataObjectDecorator {

	function updateCMSFields(&$fields) {
		$obj = DataObject::get_one("TemplateOverviewDescription", "ClassNameLink = '".$this->owner->ClassName."'");
		if($obj) {
			if($obj->ToDoListHyperLink) {
				$fields->replaceField("ToDo", new LiteralField("ToDo", '<p><a href="'.$obj->ToDoListHyperLink.'" target="todo">review to do items...</a></p>'));
			}
			$fields->addFieldToTab("Root.Help", new LiteralField("MoreHelp", $obj->renderWith("TemplateOverviewPageCMSDetail")));
		}
	}


}
