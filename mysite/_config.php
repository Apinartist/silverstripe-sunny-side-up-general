<?php


/* startup */
global $project; $project = 'mysite';
global $database; $database = "staging_silverstripe_webdevelopment_com";
require_once("conf/ConfigureFromEnv.php");



/* sapphire */
SSViewer::set_theme('main');
Geoip::$default_country_code = "NZ";
ContentNegotiator::disable();
GD::set_default_quality(85);
Email::setAdminEmail('swd@sunnysideup.co.nz');
$protocolAndHost = Director::protocolAndHost();

//===================---------------- start testemail ----------------===================
$devsitearray = array("http://localhost");
if(in_array($protocolAndHost, $devsitearray)) {
	Email::set_mailer("TestMailer");
}
unset($devsitearray);
unset($protocolAndHost);
//===================---------------- end testemail ----------------===================

/* CMS */
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");
ModelAdmin::set_page_length(10);

//===================---------------- START blog MODULE ----------------===================
BlogEntry::allow_wysiwyg_editing();
//===================---------------- END blog MODULE ----------------===================

//===================---------------- START googleAnalyticsbasics MODULE ----------------===================
SiteTreeDecorator::add_extension('SiteTree', 'GoogleAnalytics');
GoogleAnalytics::$googleAnalyticsCode = "UA-8998394-4"; //e.g. UA-xxxx-y
//===================---------------- END googleAnalyticsbasics MODULE ----------------===================


//===================---------------- START metatags MODULE ----------------===================
SiteTreeDecorator::add_extension('SiteTree', 'MetaTagAutomation');
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
MetaTagAutomation_controller::set_folder_for_combined_files("assets");
MetaTagAutomation_controller::set_combine_css_files_into_one(0);
MetaTagAutomation_controller::set_combine_js_files_into_one(0);
//===================---------------- END metatags MODULE ----------------===================

//===================---------------- START sharethis MODULE ----------------===================
SiteTreeDecorator::add_extension('SiteTree', 'ShareThis');
ShareThis::$EnabledIcons = Array("email", "print", "google", "facebook", "favourites");
ShareThis::$ShowTitle = false;
ShareThis::$IconTransparent = true;
ShareThis::set_include_by_default(true);
ShareThis::set_always_include (true);
ShareThis::set_share_this_all_in_one(false); // all-in-one button - see http://sharethis.com/developers/api_examples/
//===================---------------- END sharethis MODULE ----------------===================


//===================---------------- START simplestspam MODULE ----------------===================
SpamProtectorManager::set_spam_protector('SimplestSpamProtector');
SimplestSpamField::set_question_and_answer("Sunny Side Up is based in what country ...", "New Zealand");
SimplestSpamField::set_explanation_message("this question is here to prevent spam");
SimplestSpamField::set_wrong_answer_message("please check anti-spam field to proceed");
//===================---------------- END simplestspam MODULE ----------------===================

//===================---------------- START templateoverview MODULE ----------------===================
//===================---------------- END templateoverview MODULE ----------------===================
