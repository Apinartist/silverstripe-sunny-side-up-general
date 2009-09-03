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
DataObject::add_extension('SiteTree', 'MetaTagger');
Object::add_extension('ContentController', 'MetaTagger_Controller');
MetaTagger::$country = "New Zealand";
MetaTagger::$copyright = 'owner';
MetaTagger::$design = 'www.sunnysideup.co.nz';
MetaTagger::$project = 'mysite';
MetaTagger::$coding = "www.sunnysideup.co.nz";
//===================---------------- END metatags MODULE ----------------===================
