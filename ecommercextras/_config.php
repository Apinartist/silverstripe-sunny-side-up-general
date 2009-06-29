<?php


/**
* developed by www.sunnysideup.co.nz
* author: Nicolaas modules [at] sunnysideup.co.nz
*
**/

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START ecommercextra MODULE ----------------===================
// *** PRODUCT EXTENSIONS
//PhotographicProductGroup::set_can_create(true);
//PhotographicProduct::set_can_create(true);
//StorageProductGroup::set_can_create(true);
//StorageProduct::set_can_create(true);

// *** EXTENSIONS
//DataObject::add_extension('SiteTree', 'AjaxOrder');
//Object::add_extension('ContentController', 'AjaxOrder_Controller');
//Order::set_modifiers(array('RecommendedProducts','BonusProductModifier', 'PickUpOrDelivery','GSTTaxModifier'));

// *** PICK UP OR DELIVERY
//PickUpOrDelivery::set_pickup_option($code = "WI", $name = "West Island", $cost = 60, $minimum = 0, $maximum = 500, $isPercentage = false);
//PickUpOrDelivery::set_default_amount(10);
//PickUpOrDelivery::set_default_title("New Zealand Delivery");

// *** RECOMMENDED PROCUCTS
//RecommendedProducts_Form::set_more_details_link_text("see details");
//RecommendedProducts_Form::set_nothing_recommended_text(" ");
//RecommendedProducts_Form::set_something_recommended_text("Recommended Additions");
//RecommendedProducts_Form::set_add_button_text("Add Selected Items");

// *** BONUS PROCUCTS
//BonusProductModifier::set_title("Bonus Product Savings");

// *** GST TAX MODIFIER
//GSTTaxModifier::set_for_country($country = "NZ", $rate = "12.5", $name = "GST", $inclexcl = "inclusive");

//NewsletterSignupModifier
//NewsletterSignupModifier::set_newsletter_tickbox_label("Would you like to receive our newsletter?");

//===================---------------- END ecommercextra MODULE ----------------===================

