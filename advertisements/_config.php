<?php



/*
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 *
 **/

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START advertisements MODULE ----------------===================
Director::addRules(10, array(
	'advertisements//$Action/$ID' => 'AdvertisementController',
));
//**** NB, dont forget to theme the templates (and also JS, as shown below!)
//MUST SET
//Object::add_extension('SiteTree', 'AdvertisementDecorator');
//MAY SET
//AdvertisementDecorator::set_use_custom_javascript(false); //if true, this will search for myproject/javascript/SlideShow.js myproject is usually called mysite.
//AdvertisementDecorator::set_page_classes_without_advertisements(array("UserDefineForm", "ErrorPage")); // excluded from the specified classes
//AdvertisementDecorator::set_page_classes_with_advertisements(array("HomePage")); //ONLY shown on specified classes
//Advertisement::set_width(100);
//Advertisement::set_height(100);
//AdvertisementStyle::add_to_array_of_js_file_options("slideshow style 1", "mysite/javascript/slideshow1.js");
//AdvertisementStyle::add_to_array_of_js_file_options("slideshow style 2", "mysite/javascript/slideshow1.js");
//===================---------------- END advertisements MODULE ----------------===================
