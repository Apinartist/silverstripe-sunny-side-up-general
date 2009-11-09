<?php


/* startup */
global $project; $project = 'mysite';
global $database; $database = "CrossSlideShow";
require_once("conf/ConfigureFromEnv.php");



/* sapphire */
SSViewer::set_theme('main');
Geoip::$default_country_code = "AU";
ContentNegotiator::disable();
GD::set_default_quality(85);
Email::setAdminEmail('test@ryanbriggs.com.au');


/* CMS */
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");
ModelAdmin::set_page_length(100);
