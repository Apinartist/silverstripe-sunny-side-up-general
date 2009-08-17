<?php


/**
* developed by www.sunnysideup.co.nz
* author: Nicolaas modules [at] sunnysideup.co.nz
*
**/

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START ecommercextra MODULE ----------------===================
// *** EXTENSIONS
//DataObject::add_extension('SiteTree', 'AjaxOrder');
//Object::add_extension('ContentController', 'AjaxOrder_Controller');
//Order::set_modifiers(array('RecommendedProducts','BonusProductModifier', 'PickUpOrDelivery','GSTTaxModifier'));

// *** PICK UP OR DELIVERY
// can set one or more pickup options.
//PickUpOrDeliveryModifier::set_pickup_option($code = "pickup", $name = "Pick-Up", $minimumDeliveryCharge = 0, $maximumDeliveryCharge = 999999999, $minimumOrderAmountForZeroRate = 999999999, $weightMultiplier =  0 , $percentage = 0.2, $fixedCost = 0);
//PickUpOrDeliveryModifier::set_default_amount(10);
//PickUpOrDeliveryModifier::set_default_title("New Zealand Delivery");
//PickUpOrDeliveryModifier_Form::set_ajaxcart_template_name("AjaxCheckoutCart");

// *** RECOMMENDED PROCUCTS
//RecommendedProductsModifier_Form::set_more_details_link_text("see details");
//RecommendedProductsModifier_Form::set_nothing_recommended_text(" ");
//RecommendedProductsModifier_Form::set_something_recommended_text("Recommended Additions");
//RecommendedProductsModifier_Form::set_add_button_text("Add Selected Items");
//RecommendedProductsModifier_Form::set_order_item_classname("Product_OrderItem");

// *** BONUS PROCUCTS
//BonusProductModifier::set_order_item_classname("Product_OrderItem");

// *** GST TAX MODIFIER
//GSTTaxModifier::set_for_country($country = "NZ", $rate = "12.5", $name = "GST", $inclexcl = "inclusive");
//GSTTaxModifier::set_default_rate(0);
//GSTTaxModifier::set_default_is_exclusive(0);

//NewsletterSignupModifier
//NewsletterSignupModifier::set_newsletter_tickbox_label("Would you like to receive our newsletter?");

// *** Ajax Order
//AjaxOrder_Controller::set_order_item_classname("ManageablePriceOrderItem");
//AjaxOrder_Controller::set_product_classname("Product");

//===================---------------- END ecommercextra MODULE ----------------===================

