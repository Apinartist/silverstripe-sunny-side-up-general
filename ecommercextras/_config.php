<?php


/**
 * @author Nicolaas modules [at] sunnysideup.co.nz
 *
 **/


//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START ecommercextra MODULE ----------------===================

// *** EXTENSIONS
//Object::add_extension('SiteTree', 'AjaxOrderDecorator');
//Object::add_extension('ContentController', 'AjaxOrderDecorator_Controller');

// *** PICK UP OR DELIVERY
//NOTE: add http://svn.gpmd.net/svn/open/multiselectfield/tags/0.2/ for nicer interface
//PickUpOrDeliveryModifier::set_form_header("Delivery Option (REQUIRED)");
//StoreAdmin::add_managed_models("PickUpOrDeliveryModifierOptions");

// *** RECOMMENDED PROCUCTS
//RecommendedProductsModifier_Form::set_nothing_recommended_text(" ");
//RecommendedProductsModifier_Form::set_something_recommended_text("Recommended Additions");
//RecommendedProductsModifier_Form::set_add_button_text("Add Selected Items");
//RecommendedProductsModifier_Form::set_order_item_classname("Product_OrderItem");

// *** BONUS PROCUCTS
//BonusProductModifier::set_order_item_classname("Product_OrderItem");
//BonusProductModifier::set_bonus_price(0);

// *** GST TAX MODIFIER
//GSTTaxModifier::set_default_country_code("NZ");
//GSTTaxModifier::set_fixed_country_code("NZ");
//GSTTaxModifier::set_exclusive_explanation(" (to be added to prices above)");
//GSTTaxModifier::set_inclusive_explanation(" (included in prices above)");
//GSTTaxModifier::set_based_on_country_note(" - based on a sale to: ");
//GSTTaxModifier::set_no_tax_description("tax-exempt");
//GSTTaxModifier::set_refund_title("Tax Exemption");
//GSTTaxModifier::set_order_item_function_for_tax_exclusive_portion("PortionWithoutTax");
//StoreAdmin::add_managed_models("GSTTaxModifierOptions");

// *** NEWSLETTER SIGNUP MODIFIER
//NewsletterSignupModifier::set_newsletter_tickbox_label("Would you like to receive our newsletter?");

// *** BLOCK COUNTRIES MODIFIER
//BlockCountriesModifier::set_must_include_country_codes(array("NZ"));
//BlockCountriesModifier::set_must_not_country_codes(array("AU"));
//BlockCountriesModifier::set_sorry_message_start("Sorry, sales to");
//BlockCountriesModifier::set_sorry_message_end("are not allowed");
//BlockCountriesModifier::set_your_country_description_default("your country");

// *** MODIFIER RULES MODIFIER
/*
ModifierRulesModifier::set_rule(
	$nameModifierA = "PickUpOrDeliveryModifier",
	$functionOrVariableNameA = "PickupOrDeliveryType",
	$valueA = array("NZ"),
	$nameModifierB = "GSTTaxModifier",
	$functionOrVariableNameB = "override_country",
	$valueB = "NZ"
);
*/

// *** LIVE EMAIL CHECK MODIFIER
/*
Director::addRules(50, array('liveemailcheckmodifier//$Action/$ID' => 'LiveEmailCheckModifier_handler'));
LiveEmailCheckModifier::set_account_exists_message("There is already an account for [email].  Would you like to login now?");
*/

// *** AJAX ORDER
//AjaxOrderDecorator_Controller::set_loading_cart_text("Loading Cart");
//AjaxOrderDecorator_Controller::set_in_cart_text("In Cart");
//AjaxOrderDecorator_Controller::set_confirm_delete_text("Are you sure you want to delete this item?");
//AjaxOrderDecorator_Controller::set_order_item_classname("Product_OrderItem");
//AjaxOrderDecorator_Controller::set_product_classname("Product");


//===================---------------- END ecommercextra MODULE ----------------===================
/*
 * NOTA BENE :: NOTA BENE :: NOTA BENE :: NOTA BENE :: NOTA BENE ::
 * @important: in the order templates, change as follows:
 * FROM: <td id="$TableTotalID" class="price"><% if IsChargable %>$Amount.Nice<% else %>-$Amount.Nice<% end_if %></td>
 * TO: <td id="$TableTotalID" class="price">$TableValue</td>
 * IN: OrderInformation_Editable.ss, Order_Content.ss
 **/

