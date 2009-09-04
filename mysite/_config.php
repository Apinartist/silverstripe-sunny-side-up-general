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

/* CMS */
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");


//===================---------------- START metatags MODULE ----------------===================
DataObject::add_extension('SiteTree', 'MetaTagAutomation');
Object::add_extension('ContentController', 'MetaTagAutomation_controller');
/* pop-ups and form interaction */
MetaTagAutomation::set_disable_update_popup(0);
/* default value for auto-update pages' metatags */
MetaTagAutomation::set_default_state_auto_update_checkbox($var) {self::$default_state_auto_update_checkbox = $var;}
/* meta-title */
MetaTagAutomation::set_update_meta_title(0);
MetaTagAutomation::set_prepend_to_meta_title("");
MetaTagAutomation::set_append_to_meta_title("");
/* meta descriptions */
MetaTagAutomation::set_update_meta_desc(0)
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


//===================---------------- START googleAnalyticsbasics MODULE ----------------===================
DataObject::add_extension('SiteTree', 'GoogleAnalytics');
GoogleAnalytics::$googleAnalyticsCode = "UA-8998394-4"; //e.g. UA-xxxx-y
//===================---------------- END googleAnalyticsbasics MODULE ----------------===================


//===================---------------- START sharethis MODULE ----------------===================
DataObject::add_extension('SiteTree', 'ShareThis');
ShareThis::$EnabledIcons = Array("email", "print", "google", "facebook", "favourites");
ShareThis::$ShowTitle = false;
ShareThis::$IconTransparent = true;
ShareThis::set_include_by_default(true);
ShareThis::set_always_include (true);
ShareThis::set_share_this_all_in_one(true); // all-in-one button - see http://sharethis.com/developers/api_examples/
//===================---------------- END sharethis MODULE ----------------===================

