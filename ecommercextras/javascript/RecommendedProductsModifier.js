/**
* Note: can not do ajax query, because it changes the recommended products
* reload page if products have been added
*
*
**/

(function($){
	$(document).ready(
		function() {
			RecommendedProductsModifier.init();
		}
	);
})(jQuery);

var RecommendedProductsModifier = {

	loadingClass: "loading",
	formID: "RecommendedProductsModifier_Form_RecommendedProducts",

	formButtonID: "RecommendedProductsModifier_Form_RecommendedProducts_action_processOrder",

	classToAddIfNoBoxesTicked: "requiredTickBox",

	msgIfNoBoxIsTicked: "please select at least one product to add",

	msgToConfirmAdding: "you are about to add more products to your cart, this will erase any data you have entered on this page - are you happy with that?",

	anyBoxTicked: false,

	init: function() {
		jQuery("#" + RecommendedProductsModifier.formID + " .checkbox input").removeClass(RecommendedProductsModifier.classToAddIfNoBoxesTicked);
		jQuery("#" + RecommendedProductsModifier.formButtonID).click(
			function() {
				return RecommendedProductsModifier.checkForTickedBoxes();
			}
		);
	},

	checkForTickedBoxes: function() {
		RecommendedProductsModifier.ajaxForm();
		RecommendedProductsModifier.anyBoxTicked = false;
		jQuery("#" + RecommendedProductsModifier.formID + " .checkbox input").each(
			function() {
				if(jQuery(this).is(":checked")) {
					RecommendedProductsModifier.anyBoxTicked = true;
				}
			}
		);
		if(true == RecommendedProductsModifier.anyBoxTicked) {
			return confirm(RecommendedProductsModifier.msgToConfirmAdding);
		}
		else {
			alert(RecommendedProductsModifier.msgIfNoBoxIsTicked);
			jQuery("#" + RecommendedProductsModifier.formID + " .checkbox input").addClass(RecommendedProductsModifier.classToAddIfNoBoxesTicked);
			return false;
		}
	}


	ajaxForm: function() {
		var options = {
			beforeSubmit:  RecommendedProductsModifier.showRequest,  // pre-submit callback
			success: RecommendedProductsModifier.showResponse,  // post-submit callback
			dataType: "html"
		};
		jQuery("#" + RecommendedProductsModifier.formID).ajaxForm(options);
	},

	// pre-submit callback
	showRequest: function (formData, jqForm, options) {
		jQuery("#" + RecommendedProductsModifier.formID).addClass(RecommendedProductsModifier.loadingClass);
		return true;
	},

	// post-submit callback
	showResponse: function (responseText, statusText)  {
		//redo quantity boxes
		//jQuery("#" + PickUpOrDeliveryModifier.updatedDivID).css("height", "auto");
		jQuery("#" + RecommendedProductsModifier.formID).removeClass(RecommendedProductsModifier.loadingClass);
		//AjaxCheckout.setChanges(responseText);
	},


}
