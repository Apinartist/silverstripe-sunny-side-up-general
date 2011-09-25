<?php

/**

AnyPriceProductPage
AnyPriceProductPage_Live
AnyPriceProductPage_versions
BillingAddress
BlockCountriesModifier
BonusProductModifier
CartPage
CartPage_Live
CartPage_versions
CheckoutPage
CheckoutPage_Live
CheckoutPage_versions
DefaultRecordsForEcommerce
DPSPayment
DpsPxPayPayment
DpsPxPayStoredCard
DPSRecurringPayment
EcommerceRegion
EcommerceVote
Email_BounceRecord
ErrorPage
ErrorPage_Live
ErrorPage_versions
File
FlatTaxModifier
Group
Group_Members
Group_Roles
GSTTaxModifier
GSTTaxModifierOptions
ImprovedPaystationHostedPayment
LoginAttempt
Member
MemberPassword
MetaTagsLinks
MinMaxModifier
ModifierExample
ModifierRulesModifier
NewsletterSignupModifier
Order
OrderAddress
OrderAttribute
OrderAttribute_Group
OrderCoupon
OrderCouponModifier
OrderEmailRecord
OrderItem
OrderModifier
OrderStatusLog
OrderStatusLog_DispatchElectronicOrder
OrderStatusLog_DispatchPhysicalOrder
OrderStatusLog_PaymentCheck
OrderStatusLog_Submitted
OrderStep
OrderStep_SentInvoice
OrderStep_SentReceipt
OrderStep_Submitted
PageComment
Payment
PayPalPayment
PaystationPayment
Permission
PermissionRole
PermissionRoleCode
PickUpOrDeliveryModifier
PickUpOrDeliveryModifierOptions
PickUpOrDeliveryModifierOptions_AvailableInCountries
Product
ProductAttributeType
ProductAttributeValue
ProductGroup
ProductGroup_Live
ProductGroup_versions
ProductStockCalculatedQuantity
ProductStockManualUpdate
ProductStockOrderEntry
ProductStockVariationCalculatedQuantity
ProductVariation
ProductVariation_AttributeValues
ProductVariation_versions
Product_Live
Product_ProductGroups
Product_VariationAttributes
Product_versions
QueuedEmail
RecurringPayment
RedirectorPage
RedirectorPage_Live
RedirectorPage_versions
RepeatOrder
RepeatOrdersPage
RepeatOrdersPage_Live
RepeatOrdersPage_versions
RepeatOrder_OrderItem
RepeatOrder_OrderItem_versions
RepeatOrder_versions
ShippingAddress
SimpleShippingModifier
SiteConfig
SiteConfig_CreateTopLevelGroups
SiteConfig_EditorGroups
SiteConfig_ViewerGroups
SiteTree
SiteTree_EditorGroups
SiteTree_ImageTracking
SiteTree_LinkTracking
SiteTree_Live
SiteTree_versions
SiteTree_ViewerGroups
TaxModifier
VirtualPage
VirtualPage_Live
VirtualPage_versions
WeightShippingModifier
*/


class CleanEcommerceTables extends Controller {

	protected $Tarray = array(
		"Order",
		"OrderAttribute",
		"OrderAttribute_Group",
		"OrderStatusLog",
		"OrderStatusLog_Dispatch",
		"OrderStep",
		"ShippingAddress",
		"Payment",
		"RecurringPayment",
		"OrderItem",
		"OrderModifier",
		"Product_OrderItem",
		"ProductVariation_OrderItem",
		"SimpleShippingModifier",
		"SubTotalModifier",
		"TaxModifier",
		"WeightShippingModifier",
		"BlockCountriesModifier",
		"BonusProductModifier",
		"GSTTaxModifier",
		"LiveEmailCheckModifier",
		"ModifierRulesModifier",
		"NewsletterSignupModifier",
		"PickUpOrDeliveryModifier",
		"RecommendedProductsModifier",
		"OrderStatusLog_PaymentCheck",
		"OrderStatusLog_DispatchElectronicOrder",
		"OrderStatusLog_DispatchPhysicalOrder",
		"OrderStep_Created",
		"OrderStep_Submitted",
		"OrderStep_SentInvoice",
		"OrderStep_Paid",
		"OrderStep_SentReceipt",
		"OrderStep_Confirmed",
		"OrderStep_Sent",
		"PayPalPayment",
		"PaystationHostedPayment",
		"PaystationPayment",
		"WorldpayPayment",
		"OgonePayment",
		"DPSRecurringPayment"

	);

	function init() {
		parent::init();
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
	}

	public function reset(){
		$sql = mysql_query("SHOW TABLES") or die(mysql_error());
		while($row = mysql_fetch_array($sql)){
			DB::query("TRUNCATE TABLE \"".$row[0]."\";");
			DB::alteration_message("deleting ".$row[0], "deleted");
		}
		echo "<hr /><hr /><hr /><hr /><hr /><a href=\"/dev/build\">build database</a>";
	}



	function deleteall () {

		foreach($this->Tarray as $t) {
			if(mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$t."'"))) {
				DB::query("DROP TABLE IF EXISTS \"$t\"");
				DB::alteration_message("deleted $t", "deleted");
			}
			else {
				DB::alteration_message("could not find $t", "edited");
			}
			if(mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$t."'"))) {
				DB::query("DROP TABLE IF EXISTS \"$t\"");
				DB::alteration_message("deleted $t", "deleted");
			}
			else {
				DB::alteration_message("could not find $t", "edited");
			}
		}
	}

}
