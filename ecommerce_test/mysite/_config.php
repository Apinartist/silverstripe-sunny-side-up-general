<?php

global $project;
$project = 'mysite';

global $database;
$database = 'ecommerce';
require_once('conf/ConfigureFromEnv.php');
MySQLDatabase::set_connection_charset('utf8');
// This line set's the current theme. More themes can be
// downloaded from http://www.silverstripe.org/themes/
SSViewer::set_theme('main');
// enable nested URLs for this site (e.g. page/sub-page/)
SiteTree::enable_nested_urls();


// __________________________________START ECOMMERCE MODULE CONFIG __________________________________


// copy the lines below to your mysite/_config.php file and set as required.
// __________________________________START ECOMMERCE MODULE CONFIG __________________________________
//The configuration below is not required, but allows you to customise your ecommerce application - check for the defalt value first.
// * * * DEFINITELY MUST SET
//Order::set_modifiers(array("MyModifierOne", "MyModifierTwo");


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
//Currency::setCurrencySymbol("�");

// * * * SHOPPING CART AND ORDER
//EcomQuantityField::set_hide_plus_and_minus(true);
//Order::set_add_shipping_fields(true);
//Order::set_table_overview_fields(array('Total' => 'Total','Status.Name'));//
//Order::set_maximum_ignorable_sales_payments_difference(0.001);//sometimes there are small discrepancies in total (for various reasons)- here you can set the max allowed differences
//Order::set_order_id_start_number(1234567);//sets a start number for order ID, so that they do not start at one.

// * * * CHECKOUT
//ExpiryDateField::set_short_months(true); //uses short months (e.g. Jan instead of january) for credit card expiry date.

// * * * MEMBER
//EcommerceRole::set_customer_group_name("Customers");
//EcommerceRole::set_admin_group_name("Shop Administrators");
//EcommerceRole::set_fixed_country_code("NZ"); //country is fixed
//EcommerceRole::set_allowed_country_codes("NZ" => "NZ", "UK," => "UK"); //country is fixed
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
//ProductGroup::set_include_child_groups(true);
//ProductGroup::only_show_products_that_can_purchase(true);
//ProductGroup::add_sort_option( $key = "price", $title = "Lowest Price", $sql = "Price ASC");
//ProductGroup::remove_sort_option( $key = "title");
//ProductGroup::set_sort_options_default( $key = "price");
//ProductGroup::set_only_show_products_that_can_purchase(true);

// * * * EMAILS
//Order_Email::set_send_all_emails_plain(true);
//Order_Email::set_copy_to_admin_for_all_emails(false);

// * * * LOGS
//OrderStatusLog::set_available_log_classes_array(array("OrderStatusLog_PaymentCheck"));

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
