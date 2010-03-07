<?php


/**
 * @author Nicolaas modules [at] sunnysideup.co.nz
 *
 **/

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START ecommercextra MODULE ----------------===================

// *** REMINDERS FOR THINGS TO SET IN ECOMMERCE:
//ShoppingCart::set_country("NZ");
//Order::set_modifiers(array('PickUpOrDelivery', 'RecommendedProducts','BonusProductModifier','GSTTaxModifier', 'NewsletterSignupModifier', 'LiveEmailCheckModifier'));
//Order::set_email("websales@silverstripe.co.nz");
//Order::set_subject("Thank you for your order at silverstripe.co.nz");


// *** EXTENSIONS
//Object::add_extension('SiteTree', 'AjaxOrder');
//Object::add_extension('ContentController', 'AjaxOrder_Controller');
//Object::add_extension('Order', 'OrderDecorator');
//Object::add_extension('Payment', 'PaymentDecorator');

// *** ORDER ADDITIONS
//OrderDecorator::set_order_id_start_number(1000);
//Order::$db["Status"] = 'Enum("New,Unpaid,PaymentConfirmed,QueryForCustomer,PartsOnOrder,Processing,Sent,Complete,AdminCancelled,MemberCancelled","New")';

// *** ORDER FORM WITHOUT SHIPPING ADDRESS
//OrderFormWithoutShippingAddress::add_extra_field("tabName", new TextField("ExampleName");
//OrderFormWithoutShippingAddress::set_fixed_country_code("NZ");
//OrderFormWithoutShippingAddress::set_postal_code_url("http://www.nzpost.co.nz/Cultures/en-NZ/OnlineTools/PostCodeFinder");
//OrderFormWithoutShippingAddress::set_postal_code_label("find postcode");

// *** PICK UP OR DELIVERY
//NOTE: add http://svn.gpmd.net/svn/open/multiselectfield/tags/0.2/ for nicer interface
//PickUpOrDeliveryModifier::set_form_header("Delivery Option (REQUIRED)");

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
	$functionOrVariableNameA "PickupOrDeliveryType",
	$valueA = array("NZ"),
	$nameModifierB = "GSTTaxModifier",
	$functionOrVariableNameB = "override_country",
	$valueB = "NZ"
);
*/

// *** LIVE EMAIL CHECK MODIFIER
/*
Director::addRules(120, array(
	'liveemailcheckmodifier//$Action/$ID' => 'LiveEmailCheckModifier_handler',
));
LiveEmailCheckModifier::set_account_exists_message("There is already an account for [email].  Would you like to login now?");
*/

// *** AJAX ORDER
//AjaxOrder::set_loading_cart_text("Loading Cart");
//AjaxOrder::set_in_cart_text("In Cart");
//AjaxOrder::set_confirm_delete_text("Are you sure you want to delete this item?");
//AjaxOrder_Controller::set_order_item_classname("Product_OrderItem");
//AjaxOrder_Controller::set_product_classname("Product");

// *** MODEL ADMIN
//ProductsAndGroupsModelAdmin::set_managed_models(Array("Product", "ProductGroup"));
//EcommerceModifiersModelAdmin::set_managed_models(array("PickUpOrDeliveryModifierOptions", "GSTTaxModifierOptions"));


// *** REPORTS
Director::addRules(50, array(
	'SalesReport_Handler//$Action/$ID' => 'SalesReport_Handler',
	//'silverstripe/HourlyTask/$Action/$ID' => 'HourlyTask' // optional..
	// setup cron job 0 * * * * wget http://www.mysite.com/silverstripe/HourlyTask/
	//if you add the OrderDecorator then this also runs this task at Order::onAfterWrite();
));

//===================---------------- END ecommercextra MODULE ----------------===================
/*
 * NOTA BENE :: NOTA BENE :: NOTA BENE :: NOTA BENE :: NOTA BENE ::
 * @important: in the order templates, change as follows:
 * FROM: <td id="$TableTotalID" class="price"><% if IsChargable %>$Amount.Nice<% else %>-$Amount.Nice<% end_if %></td>
 * TO: <td id="$TableTotalID" class="price">$TableValue</td>
 * IN: OrderInformation_Editable.ss, Order_Content.ss
 **/