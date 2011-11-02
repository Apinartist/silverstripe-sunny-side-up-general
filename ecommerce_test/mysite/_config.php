<?php

global $project; $project = 'mysite';
global $database; $database = 'ecommerce';
require_once('conf/ConfigureFromEnv.php');

//===================---------------- START mysite MODULE ----------------===================
Director::addRules(50, array(
	'build-ecommerce//$Action/$ID/$OtherID' => 'CleanEcommerceTables'
));

Object::add_extension('SiteConfig', 'SiteConfigExtras');
//===================---------------- END mysite MODULE ----------------===================

//===================---------------- START sapphire MODULE ----------------===================
MySQLDatabase::set_connection_charset('utf8');
SSViewer::set_theme('main');
Geoip::$default_country_code = "NZ";
GD::set_default_quality(85);
Email::setAdminEmail('swd@sunnysideup.co.nz');
//Member::set_password_validator( new NZGovtPasswordValidator());
SiteTree::$breadcrumbs_delimiter = ' <span class="delimiter">&raquo;</span> ';
Session::set_timeout(1209600);//60 * 60 * 24 * 14
Email::bcc_all_emails_to('copyonly@sunnysideup.co.nz');
Requirements::set_combined_files_folder("_cache");
//Director::forceWWW();
FulltextSearchable::enable();
if(Director::isDev()) {
	SSViewer::set_source_file_comments(true);
}
if(Director::isLive()) {
	SS_Log::add_writer(new SS_LogEmailWriter('errors@sunnysideup.co.nz'), SS_Log::ERR);
}
else {
	BasicAuth::protect_entire_site();
}
SiteTree::enable_nested_urls();
i18n::set_locale('en_NZ');
//===================---------------- END sapphire MODULE ----------------===================


//===================---------------- START cms MODULE ----------------===================
LeftAndMain::setApplicationName("Sunny Side Up Test Website");
LeftAndMain::set_loading_image("themes/main/images/logo.gif");
ModelAdmin::set_page_length(100);
CMSMenu::remove_menu_item("CommentAdmin");
CMSMenu::remove_menu_item("ReportAdmin");
CMSMenu::remove_menu_item("HelpAdmin");
LeftAndMain::setLogo($location = "", $style = "");
PageComment::enableModeration();
//===================---------------- ENBD cms MODULE  ----------------===================



// __________________________________START ECOMMERCE MODULE CONFIG __________________________________


// __________________________________START ECOMMERCE MODULE CONFIG __________________________________
//The configuration below is not required, but allows you to customise your ecommerce application - check for the defalt value first.
// * * * DEFINITELY MUST SET
Order::set_modifiers(array("PickUpOrDeliveryModifier", "ModifierExample", "GSTTaxModifier", "OrderMarker", "DiscountCouponModifier"));


// * * * HIGHLY RECOMMENDED SETTINGS NON-ECOMMERCE
//Payment::set_site_currency('NZD');
Geoip::$default_country_code = 'NZ';

// * * * ECOMMERCE I18N SETTINGS
// * * * for Currency &  Date Formats get this module: http://code.google.com/p/silverstripe-i18n-fieldtypes/
//Object::useCustomClass('Currency','I18nCurrency',true);
//Object::useCustomClass('Money','CustomMoney',true);
// * * * FOR DATE FORMATS SET F.E.
//setlocale (LC_TIME, 'en_NZ@dollar', 'en_NZ.UTF-8', 'en_NZ', 'nz', 'nz');
//Object::useCustomClass('SS_Datetime','I18nDatetime',true);
//OR
//i18n::set_locale('en_NZ');
//Object::useCustomClass('SS_Datetime','ZendDate',true);
Currency::setCurrencySymbol("€");

// * * * SHOPPING CART AND ORDER
EcomQuantityField::set_hide_plus_and_minus(false);
OrderAddress::set_use_separate_shipping_address(true);
//Order::set_maximum_ignorable_sales_payments_difference(0.001);//sometimes there are small discrepancies in total (for various reasons)- here you can set the max allowed differences
//Order::set_order_id_start_number(1234567);//sets a start number for order ID, so that they do not start at one.

// * * * CHECKOUT
//ExpiryDateField::set_short_months(true); //uses short months (e.g. Jan instead of january) for credit card expiry date.

// * * * MEMBER
//EcommerceRole::set_customer_group_name("Customers");
//EcommerceRole::set_admin_group_name("Shop Administrators");
//EcommerceRole::set_fixed_country_code("NZ"); //country is fixed
//EcommerceRole::set_allowed_country_codes(array("NZ" => "NZ")); //"UK," => "UK"); //country is fixed
//EcommerceRole::set_login_invite_alternative_text('<a href="http://www.mysite.com/Security/login/?BackURL=">If you are a member then please log in.</a>);

// * * * MODIFIERS
//FlatTaxModifier::set_tax("0.15", "GST", $exclusive = false);
//SimpleShippingModifier::set_default_charge(10);
//SimpleShippingModifier::::set_charges_for_countries(array('US' => 10,'NZ' => 5));
//TaxModifier::::set_for_country($country = "NZ", $rate = 0.15, $name = "GST", $inclexcl = "inclusive"))

// * * * SPECIAL CASES
//OrderItem::disable_quantity_js();
//ShoppingCartset_response_class("EcommerceResponse");

// * * * PRODUCTS
//ProductsAndGroupsModelAdmin::set_managed_models(array(("Product", "ProductGroup");
//SS_Report::register("SideReport", "EcommerceSideReport_AllProducts");
//SS_Report::register("SideReport", "EcommerceSideReport_FeaturedProducts");
//SS_Report::register("SideReport", "EcommerceSideReport_NoImageProducts");
//Product_Image::set_thumbnail_size(140, 100);
//Product_Image::set_content_image_width(200);
//Product_Image::set_large_image_width(200);
ProductGroup::add_sort_option( $key = "popular", $title = "Most Popular", $sql = "\"NumberSold\" DESC");
ProductGroup::add_sort_option( $key = "featured", $title = "Featured", $sql = "\"FeaturedProduct\" DESC, \"Title\" DESC");
//ProductGroup::remove_sort_option( $key = "title");

// * * * EMAILS
Email::setAdminEmail("sales@silverstripe-ecommerce.com");
//Order_Email::set_send_all_emails_plain(true);
//Order_Email::set_copy_to_admin_for_all_emails(false);


// * * * PROCESS: LOGS AND STATUS
//OrderStatusLog::set_available_log_classes_array(array("OrderStatusLog_PaymentCheck", "OrderStatusLog_CheckAvailability"));
//OrderStatusLog::set_order_status_log_class_used_for_submitting_order("OrderStatusLog_PaymentCheck");
//OrderStep::set_order_steps_to_include(array("OrderStep_Created","OrderStep_Submitted","OrderStep_SentInvoice","OrderStep_Paid","OrderStep_Sent","OrderStep_Archived"));
//OrderStep::add_order_step_to_include("OrderStep_CheckAvailability", $placeAfter = "OrderStep_Created");
//OrderStep::remove_order_step_to_include("OrderStep_SentInvoice");

// * * * HELP
// __________________________________ END ECOMMERCE MODULE CONFIG __________________________________



// __________________________________ END PAYMENT MODULE CONFIG __________________________________
Payment::set_site_currency("NZD");

Payment::set_supported_methods(array(
	'PayPalPayment' => 'Paypal Payment'
));
// __________________________________ END PAYMENT MODULE CONFIG __________________________________




//===================---------------- START metatags MODULE ----------------===================
Object::add_extension('SiteTree', 'MetaTagAutomation');
Object::add_extension('ContentController', 'MetaTagAutomation_controller');
/* pop-ups and form interaction */
//MetaTagAutomation::set_disable_update_popup(0);
/* default value for auto-update pages' metatags */
//MetaTagAutomation::set_default_state_auto_update_checkbox(0);
/* meta-title */
//MetaTagAutomation::set_update_meta_title(0);
//MetaTagAutomation::set_prepend_to_meta_title("");
//MetaTagAutomation::set_append_to_meta_title("");
/* meta descriptions */
//MetaTagAutomation::set_update_meta_desc(0);
//MetaTagAutomation::set_meta_desc_length(12);
/* meta keywords */
//MetaTagAutomation::set_update_meta_keys(0);
//MetaTagAutomation::set_number_of_keywords(12);
//MetaTagAutomation::set_min_word_char(3);
//MetaTagAutomation::set_exclude_words("the,and,from");
/* additional metatag information */
//MetaTagAutomation_controller::set_country("New Zealand");
//MetaTagAutomation_controller::set_copyright("owner");
//MetaTagAutomation_controller::set_design("owner");
//MetaTagAutomation_controller::set_coding("owner");
/* fonts */
MetaTagAutomation::add_google_font("Yanone+Kaffeesatz:200,300,400,700");
MetaTagAutomation::add_google_font("Quattrocento+Sans");
/* combined files */
//MetaTagAutomation_controller::set_folder_for_combined_files("cache");
//MetaTagAutomation_controller::set_combine_css_files_into_one(0);
//MetaTagAutomation_controller::set_combine_js_files_into_one(0);
//===================---------------- END metatags MODULE ----------------===================






//===================---------------- START payment MODULE ----------------===================
Payment::set_site_currency("NZD");
Payment::set_supported_methods(array(
	'DirectCreditPayment' => 'Direct Credit (payment into bank account)',
	'InStorePayment' => 'Pay in Store on Pick-Up'
));
//===================---------------- END payment MODULE ----------------===================







// __________________________________ START ECOMMERCE PRODUCT VARIATIONS MODULE CONFIG __________________________________
//MAY SET
ProductsAndGroupsModelAdmin::add_managed_model("ProductAttributeValue");
ProductsAndGroupsModelAdmin::add_managed_model("ProductAttributeType");
Object::add_extension("ProductAttributeValue", "ProductAttributeDecoratorColour_Value");
Object::add_extension("ProductAttributeType", "ProductAttributeDecoratorColour_Type");
ProductAttributeDecoratorColour_Value::set_default_contrast_colour("FFFFFF");
ProductAttributeDecoratorColour_Value::set_default_colour("000000");
//ProductWithVariationDecorator_Controller::set_use_js_validation(false);
//ProductWithVariationDecorator_Controller::set_alternative_validator_class_name("MyValidatorClass");
// __________________________________ END ECOMMERCE PRODUCT VARIATIONS MODULE CONFIG __________________________________









//===================---------------- START ecommerce_Repeatorders MODULE ----------------===================
//Object::add_extension('Order', 'AutomaticallyCreatedOrderDecorator');
//RepeatOrder::set_delivery_days(array('Tuesday','Wednesday','Thursday','Friday'));
//RepeatOrder::set_payment_methods(array(
	//'DirectCreditPayment'  => 'Direct Credit (payment into bank account)',
	//'DpsPxPayStoredPayment'  => 'Saved Credit Card using DPS'
//));
//RepeatOrder::set_minimum_days_in_the_future(3);
//===================---------------- END ecommerce_Repeatorders MODULE ----------------===================





//===================---------------- START ecommerce_delivery MODULE ----------------===================
//NOTE: add http://svn.gpmd.net/svn/open/multiselectfield/tags/0.2/ for nicer interface
PickUpOrDeliveryModifier::set_form_header("Delivery Option (REQUIRED)");
StoreAdmin::add_managed_model("PickUpOrDeliveryModifierOptions");
//===================---------------- END ecommerce_delivery  MODULE ----------------===================



//===================---------------- START ecommerce_tax MODULE ----------------===================
// *** GST TAX MODIFIER
GSTTaxModifier::set_default_country_code("NL");
//GSTTaxModifier::set_fixed_country_code("NZ");
//GSTTaxModifier::set_exclusive_explanation(" (to be added to prices above)");
//GSTTaxModifier::set_inclusive_explanation(" (included in prices above)");
//GSTTaxModifier::set_based_on_country_note(" - based on a sale to: ");
//GSTTaxModifier::set_no_tax_description("tax-exempt");
//GSTTaxModifier::set_refund_title("Tax Exemption");
//GSTTaxModifier::set_order_item_function_for_tax_exclusive_portion("PortionWithoutTax");
StoreAdmin::add_managed_model("GSTTaxModifierOptions");
//===================---------------- END ecommerce_tax MODULE ----------------===================



//===================---------------- START ecommerce_product_tag MODULE ----------------===================
Object::add_extension("Product","EcommerceProductTagProductDecorator");
ProductsAndGroupsModelAdmin::add_managed_model("EcommerceProductTag");
//===================---------------- END ecommerce_product_tag MODULE ----------------===================


//===================---------------- START ecommerce_alsorecommended MODULE ----------------===================
Object::add_extension('Product', 'EcommerceAlsoRecommendedDOD');
//===================---------------- END ecommerce_alsorecommended MODULE ----------------===================

//===================---------------- START ecommerce_dimensions MODULE ----------------===================
Object::add_extension("Product","EcommerceDimensionsDecorator");
//===================---------------- END ecommerce_dimensions MODULE ----------------===================


//===================---------------- START ecommerc_stockcontrol MODULE ----------------===================
//SET BUYABLES
Object::add_extension('Product', 'BuyableStockDecorator');
BuyableStockDecorator::add_buyable("Product");
Object::add_extension('ProductVariation', 'BuyableStockDecorator');
BuyableStockDecorator::add_buyable("ProductVariation");

//HIGHLY RECOMMENDED
MinMaxModifier::set_use_stock_quantities(true); //make use of the stock quantity tables to keep track of them
ProductsAndGroupsModelAdmin::add_managed_model("BuyableStockManualUpdate");
ProductsAndGroupsModelAdmin::add_managed_model("BuyableStockOrderEntry");


//MAY SET
//MinMaxModifier::set_default_min_quantity(1);
//MinMaxModifier::set_default_max_quantity(99);
//MinMaxModifier::set_min_field("MinQuantity");
//MinMaxModifier::set_max_field("MaxQuantity");
//MinMaxModifier::set_adjustment_message("Based on stock availability, quantities have been adjusted as follows: ");
//MinMaxModifier::set_sorry_message("Sorry, your selected value not is available");
//===================---------------- END ecommerc_stockcontrol MODULE ----------------===================


//===================---------------- START ecommerce_quick_add MODULE ----------------===================
Object::add_extension("Product", "AddToCartPageDecorator");
Object::add_extension("ProductGroup", "AddToCartPageDecorator");
//===================---------------- END ecommerce_quick_add MODULE ----------------===================


//===================---------------- START ecommerce_group_pricing MODULE ----------------===================
//MUST SET - WHERE APPLICABLE
DataObject::add_extension("Product", "ComplexPriceBuyableDecorator");
DataObject::add_extension("ProductVariation", "ComplexPriceBuyableDecorator");
DataObject::add_extension("ComplexPriceObject", "ComplexPriceBuyableDecorator_ComplexPriceObject");
//===================---------------- END ecommerce_group_pricing MODULE ----------------===================


//===================---------------- START googleAnalyticsbasics MODULE ----------------===================
DataObject::add_extension('SiteTree', 'GoogleAnalytics');
GoogleAnalytics::$googleAnalyticsCode = "UA-26108878-1"; //e.g. UA-xxxx-y
//===================---------------- END googleAnalyticsbasics MODULE ----------------===================



//===================---------------- START ecommerce_discount_coupon MODULE ----------------===================
StoreAdmin::add_managed_model("DiscountCouponOption");
//DiscountCouponOptions::set_coupons_can_only_be_used_once(true);
//Order::add_modifiers("DiscountCouponModifier");
//===================---------------- END ecommerce_discount_coupon MODULE ----------------===================


