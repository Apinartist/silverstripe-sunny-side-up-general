<?php


/* startup */
global $project; $project = 'mysite';
global $database; $database = "silverstripe_webdevelopment_com";
require_once("conf/ConfigureFromEnv.php");


//===================---------------- START php MODULE ----------------===================
date_default_timezone_set("NZ");
//===================---------------- END php MODULE ----------------===================


//===================---------------- START mysite MODULE ----------------===================
Object::add_extension("SiteConfig", "SiteConfigExtras");
//===================---------------- END mysite MODULE ----------------===================

//===================---------------- START sapphire MODULE ----------------===================
SSViewer::set_theme('main');
MySQLDatabase::set_connection_charset('utf8');
Geoip::$default_country_code = "NZ";
GD::set_default_quality(85);
Email::setAdminEmail('swd@sunnysideup.co.nz');
//Member::set_password_validator( new NZGovtPasswordValidator()); //this does not really work
SiteTree::$breadcrumbs_delimiter = ' <span class="delimiter">&raquo;</span> ';
Session::set_timeout(1209600);//60 * 60 * 24 * 14
Email::bcc_all_emails_to('copyonly@sunnysideup.co.nz');
//Requirements::set_combined_files_folder("themes/main"); //DO NOT USE!!!
//Director::forceWWW();
FulltextSearchable::enable(array("SiteTree"));
if(Director::isLive()) {
	//Director::forceWWW();
	SS_Log::add_writer(new SS_LogEmailWriter('errors@sunnysideup.co.nz'), SS_Log::ERR);
}
else {
	Email::send_all_emails_to("example@best.com");
	BasicAuth::protect_entire_site();
	if(Director::isDev()) {
		SSViewer::set_source_file_comments(true);
	}
}
SiteTree::enable_nested_urls();
i18n::set_locale('en_NZ');
i18n::set_date_format("dd-MM-YYYY");
/**
 * This is a fix for i18n::include_by_locale() which would scan all modules for language
 * files every time a translation was done because there are no language files for en_NZ.
 * Add this means that we never scan for language files for the en_NZ locale.
 */
$GLOBALS['lang']['en_NZ'] = array();
//===================---------------- END sapphire MODULE ----------------===================


//===================---------------- START cms MODULE ----------------===================
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");
ModelAdmin::set_page_length(100);
CMSMenu::remove_menu_item("CommentAdmin");
CMSMenu::remove_menu_item("ReportAdmin");
//CMSMenu::remove_menu_item("HelpAdmin");
LeftAndMain::setLogo($location = "", $style = "");
PageComment::enableModeration();
HtmlEditorConfig::get('cms')->setOption('paste_auto_cleanup_on_paste','true');
HtmlEditorConfig::get('cms')->setOption('paste_remove_styles','true');
HtmlEditorConfig::get('cms')->setOption('paste_remove_styles_if_webkit','true');
HtmlEditorConfig::get('cms')->setOption('paste_strip_class_attributes','true');
//===================---------------- END cms MODULE  ----------------===================

//===================---------------- START blog MODULE ----------------===================
BlogEntry::allow_wysiwyg_editing();
//===================---------------- END blog MODULE ----------------===================

//===================---------------- START googleAnalyticsbasics MODULE ----------------===================
Object::add_extension('SiteTree', 'GoogleAnalytics');
GoogleAnalytics::$googleAnalyticsCode = "UA-8998394-4"; //e.g. UA-xxxx-y
//===================---------------- END googleAnalyticsbasics MODULE ----------------===================


//===================---------------- START metatags MODULE ----------------===================
//dont forget to add $this->addBasicMetatagRequirements() to Page_Controller->init(); and add this to your theme: $ExtendedMetatags
Object::add_extension('SiteConfig', 'MetaTagSiteConfigExtension');
Object::add_extension('SiteTree', 'MetaTagAutomation');
Object::add_extension('ContentController', 'MetaTagAutomation_controller');
/* pop-ups and form interaction */
//MetaTagAutomation::set_disable_update_popup(false);
/* meta descriptions */
//MetaTagAutomation::set_meta_desc_length(27);
/* meta keywords */
//MetaTagAutomation::set_hide_keywords_altogether(true);
//FONTS - see google fonts for options, include within CSS file as: body {font-family: Inconsolata;}
//MetaTagAutomation::add_google_font("Inconsolata");
/* combined files */
//MetaTagAutomation_controller::set_folder_for_combined_files("cache");
MetaTagAutomation_controller::set_combine_css_files_into_one(true);
MetaTagAutomation_controller::set_combine_js_files_into_one(true);
/* favicons */
//MetaTagAutomation::set_use_themed_favicon(true);
//===================---------------- END metatags MODULE ----------------===================



//===================---------------- START templateoverview MODULE ----------------===================
//MUST SET
TemplateOverviewBug::set_error_email("errors@sunnysideup.co.nz");
$isDev = Director::isDev();
if($isDev) {
	Object::add_extension('Page_Controller', 'TemplateOverviewPageExtension');
}
if($isDev) {
	Object::add_extension('SiteTree', 'TemplateOverviewPageDecorator');
	Director::addRules(7, array('error/report' => 'ErrorNotifierController'));
}
TemplateOverviewPage::set_auto_include(true);
LeftAndMain::require_css("templateoverview/css/TemplateOverviewCMSHelp.css");
//help files
CMSHelp::set_help_file_directory_name("_help");
LeftAndMain::$help_link = "admin/help/";
//===================---------------- END templateoverview MODULE ----------------===================


//===================---------------- START typography MODULE ----------------===================
TypographyTestPage::set_auto_include(true);
//===================---------------- END typography MODULE ----------------===================

//===================---------------- START userdefinedforms MODULE ----------------===================
UserDefinedForm::$required_identifier = "*";
//===================---------------- END userdefinedforms MODULE ----------------===================

