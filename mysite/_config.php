<?php


/* startup */
global $project; $project = 'mysite';
global $database; $database = "silverstripe_webdevelopment_com";
require_once("conf/ConfigureFromEnv.php");


//===================---------------- START mysite MODULE ----------------===================
Object::add_extension("SiteConfig", "SiteConfigExtras");
//===================---------------- END mysite MODULE ----------------===================

//===================---------------- START sapphire MODULE ----------------===================
MySQLDatabase::set_connection_charset('utf8');
SSViewer::set_theme('main');
Geoip::$default_country_code = "NZ";
GD::set_default_quality(85);
Email::setAdminEmail('swd@sunnysideup.co.nz');
Member::set_password_validator( new NZGovtPasswordValidator());
SiteTree::$breadcrumbs_delimiter = ' <span class="delimiter">&raquo;</span> ';
Session::set_timeout(1209600);//60 * 60 * 24 * 14
Email::bcc_all_emails_to('copyonly@sunnysideup.co.nz');
Requirements::set_combined_files_folder("_cache");
//Director::forceWWW();
FulltextSearchable::enable();
if(Director::isDev()) {
	SSViewer::set_source_file_comments(true);
}
if(Director::isLive()) {
	SS_Log::add_writer(new SS_LogEmailWriter('errors@sunnysideup.co.nz'), SS_Log::ERR);
}
else {
	BasicAuth::protect_entire_site();
}
SiteTree::enable_nested_urls();
i18n::set_locale('en_NZ');
i18n::set_date_format("dd-MM-YYYY");
//===================---------------- END sapphire MODULE ----------------===================


//===================---------------- START cms MODULE ----------------===================
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");
ModelAdmin::set_page_length(100);
CMSMenu::remove_menu_item("CommentAdmin");
CMSMenu::remove_menu_item("ReportAdmin");
CMSMenu::remove_menu_item("HelpAdmin");
LeftAndMain::setLogo($location = "", $style = "");
PageComment::enableModeration();
//===================---------------- ENBD cms MODULE  ----------------===================


//===================---------------- start testemail ----------------===================
$protocolAndHost = Director::protocolAndHost();
$devsitearray = array("http://localhost");
if(in_array($protocolAndHost, $devsitearray)) {
	Email::set_mailer( new TestMailer());
}
unset($devsitearray);
unset($protocolAndHost);
//===================---------------- end testemail ----------------===================

//===================---------------- START blog MODULE ----------------===================
BlogEntry::allow_wysiwyg_editing();
//===================---------------- END blog MODULE ----------------===================

//===================---------------- START googleAnalyticsbasics MODULE ----------------===================
Object::add_extension('SiteTree', 'GoogleAnalytics');
GoogleAnalytics::$googleAnalyticsCode = "UA-8998394-0000"; //e.g. UA-xxxx-y
//===================---------------- END googleAnalyticsbasics MODULE ----------------===================


//===================---------------- START metatags MODULE ----------------===================
Object::add_extension('SiteTree', 'MetaTagAutomation');
Object::add_extension('ContentController', 'MetaTagAutomation_controller');
/* pop-ups and form interaction */
MetaTagAutomation::set_disable_update_popup(0);
/* default value for auto-update pages' metatags */
MetaTagAutomation::set_default_state_auto_update_checkbox(0);
/* meta-title */
MetaTagAutomation::set_update_meta_title(0);
MetaTagAutomation::set_prepend_to_meta_title("");
MetaTagAutomation::set_append_to_meta_title("");
/* meta descriptions */
MetaTagAutomation::set_update_meta_desc(0);
MetaTagAutomation::set_meta_desc_length(12);
/* meta keywords */
MetaTagAutomation::set_update_meta_keys(0);
MetaTagAutomation::set_number_of_keywords(12);
MetaTagAutomation::set_min_word_char(3);
MetaTagAutomation::set_exclude_words("the,and,from");
/* additional metatag information */
MetaTagAutomation_controller::set_country("New Zealand");
MetaTagAutomation_controller::set_copyright("www.sunnysideup.co.nz");
MetaTagAutomation_controller::set_design("www.sunnysideup.co.nz");
MetaTagAutomation_controller::set_coding("www.sunnysideup.co.nz");
/* combined files */
MetaTagAutomation_controller::set_folder_for_combined_files("cache");
MetaTagAutomation_controller::set_combine_css_files_into_one(0);
MetaTagAutomation_controller::set_combine_js_files_into_one(0);
//===================---------------- END metatags MODULE ----------------===================

//===================---------------- START sharethis MODULE ----------------===================
ShareThis::set_always_include (true);
ShareThis::set_share_this_all_in_one(false); // all-in-one button - see http://sharethis.com/developers/api_examples/
DataObject::add_extension('SiteTree', 'ShareThis');
ShareThis::set_always_include (true);
ShareThis::set_include_by_default(true);

//ShareThis::set_share_this_all_in_one(true); // all-in-one button - see http://sharethis.com/developers/api_examples/
//ShareThis::set_show_title_with_icon(false);
//ShareThis::set_alternate_icons(array("live" => "mysite/images/madgif.gif"));
//ShareThis::set_use_bw_effect(true);

//hide / add completely
//ShareThis::set_icons_to_include(array("facebook", "google", "linkedin"));   //OR
//ShareThis::set_icons_to_exclude(array("myspace"));

//DataObject::add_extension('SiteTree', 'SocialNetworkingLinks');
//SocialNetworkingLinks::set_always_include (false);
//SocialNetworkingLinks::set_include_by_default(true);
//SocialNetworkingLinks::set_show_title_with_icon(false);

//optional//requires: http://sunny.svnrepository.com/svn/sunny-side-up-general/dataobjectsorter
Object::add_extension('ShareThisDataObject', 'DataObjectSorterDOD');
DataObjectSorterDOD::set_do_not_add_alternative_sort_field(true);
//



//===================---------------- START simplestspam MODULE ----------------===================
SpamProtectorManager::set_spam_protector('SimplestSpamProtector');
//===================---------------- END simplestspam MODULE ----------------===================


//===================---------------- START templateoverview MODULE ----------------===================
TemplateOverviewPage::set_auto_include(true);
if(Director::isDev()) {
	Object::add_extension('Page_Controller', 'TemplateOverviewPageExtension');
}
//===================---------------- END templateoverview MODULE ----------------===================



//===================---------------- START typography MODULE ----------------===================
TypographyTestPage::set_auto_include(true);
//===================---------------- END typography MODULE ----------------===================

//===================---------------- START userdefinedforms MODULE ----------------===================
UserDefinedForm::$required_identifier = "*";
//===================---------------- END userdefinedforms MODULE ----------------===================


//===================---------------- START picasarandomizer MODULE ----------------===================
// MUST SET
PicasaRandomImage::set_google_username("nfrancken");

//OPTIONAL
PicasaRandomImage::set_number_of_folders(3);
PicasaRandomImage::set_number_of_images_per_folder(2);
//===================---------------- END picasarandomizer MODULE ----------------===================
