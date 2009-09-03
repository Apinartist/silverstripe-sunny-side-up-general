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

/* CMS */
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");

Email::setAdminEmail('swd@sunnysideup.co.nz');
