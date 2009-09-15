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
//Order::set_modifiers(array('PickUpOrDelivery', 'RecommendedProducts','BonusProductModifier','GSTTaxModifier', 'NewsletterSignupModifier'));

//ORDER FORM WITHOUT SHIPPING ADDRESS
//OrderFormWithoutShippingAddress::add_extra_field("tabName", new TextField("ExampleName");
//OrderFormWithoutShippingAddress::set_fixed_country_code("NZ");
//OrderFormWithoutShippingAddress::set_postal_code_url("http://www.nzpost.co.nz/Cultures/en-NZ/OnlineTools/PostCodeFinder");



// *** PICK UP OR DELIVERY
// can set one or more pickup options.
//PickUpOrDeliveryModifier::set_pickup_option(
/*
		$code = "pickup",
		$name = "Pick-Up",
		$minimumDeliveryCharge = 0,
		$maximumDeliveryCharge = 999999999,
		$minimumOrderAmountForZeroRate = 999999999,
		$weightMultiplier =  0 ,
		$percentage = 0.2,
		$fixedCost = 0
	);
*/
//PickUpOrDeliveryModifier::set_default_code("pickup");
//PickUpOrDeliveryModifier_Form::set_ajaxcart_template_name("AjaxCheckoutCart");

// *** RECOMMENDED PROCUCTS
//RecommendedProductsModifier_Form::set_nothing_recommended_text(" ");
//RecommendedProductsModifier_Form::set_something_recommended_text("Recommended Additions");
//RecommendedProductsModifier_Form::set_add_button_text("Add Selected Items");
//RecommendedProductsModifier_Form::set_order_item_classname("Product_OrderItem");

// *** BONUS PROCUCTS
//BonusProductModifier::set_order_item_classname("Product_OrderItem");
//BonusProductModifier::set_bonus_price(0);

// *** GST TAX MODIFIER
//GSTTaxModifier::set_for_country($country = "NZ", $rate = "12.5", $name = "GST", $inclexcl = "inclusive");
//GSTTaxModifier::set_default_rate(0);
//GSTTaxModifier::set_default_is_exclusive(0);

// *** NewsletterSignupModifier
//NewsletterSignupModifier::set_newsletter_tickbox_label("Would you like to receive our newsletter?");

// *** Modifier Rules Modifier
// ModifierRulesModifier::set_rule(
/*
		$nameModifierA = "PickUpOrDeliveryModifier",
		$functionOrVariableNameA "PickupOrDeliveryType",
		$valueA = array("NZ"),
		$nameModifierB = "GSTTaxModifier",
		$functionOrVariableNameB = "override_country",
		$valueB = "NZ"
	);
*/

// *** Ajax Order

//AjaxOrder::set_loading_cart_text("Loading Cart");
//AjaxOrder::set_in_cart_text("In Cart");
//AjaxOrder::set_confirm_delete_text("Are you sure you want to delete this item?");
//AjaxOrder_Controller::set_order_item_classname("Product_OrderItem");
//AjaxOrder_Controller::set_product_classname("Product");
//AjaxOrder_Controller::set_product_classname("Product");

// *** Model Admin

//ProductsAndGroupsModelAdmin::set_managed_models(Array("Product", "ProductGroup"));

//===================---------------- END ecommercextra MODULE ----------------===================

