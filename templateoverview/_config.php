<?php

/**
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 *
 **/



//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START templateoverview MODULE ----------------===================
//MUST SET
//if(Director::isDev()) {Object::add_extension('Page_Controller', 'TemplateOverviewPageExtension');}
//MAY SET
//if(Director::isDev()) {Object::add_extension('SiteTree', 'TemplateOverviewPageDecorator');}
if(Director::isDev()) {Director::addRules(7, array('error/report' => 'ErrorNotifierController'));}
//TemplateOverviewPage::set_auto_include(true);
//LeftAndMain::require_css("templateoverview/css/TemplateOverviewCMSHelp.css");
//===================---------------- END templateoverview MODULE ----------------===================

