(function($){
	$(document).ready(
		function() {
			PickUpOrDeliveryModifier.init();
		}
	);
})(jQuery);


var	PickUpOrDeliveryModifier = {

	formID: "PickUpOrDeliveryModifier_Form_ModifierForm",

	dropDownIDappendix: "_PickupOrDeliveryType",

	loadingClass: "loading",

	init: function() {
		var options = {
			beforeSubmit:  PickUpOrDeliveryModifier.showRequest,  // pre-submit callback
			success: PickUpOrDeliveryModifier.showResponse,  // post-submit callback
			dataType: "json"
		};
		jQuery('#' + PickUpOrDeliveryModifier.formID).ajaxForm(options);
		jQuery("#" + PickUpOrDeliveryModifier.formID + " ." + PickUpOrDeliveryModifier.actionsClass).hide();
		jQuery("#" + PickUpOrDeliveryModifier.formID+ PickUpOrDeliveryModifier.dropDownIDappendix).change(
			function() {
				jQuery("#" + PickUpOrDeliveryModifier.formID).submit();
			}
		);
	},

	// pre-submit callback
	showRequest: function (formData, jqForm, options) {
		jQuery("#" + PickUpOrDeliveryModifier.formID).addClass(PickUpOrDeliveryModifier.loadingClass);
		return true;
	},

	// post-submit callback
	showResponse: function (responseText, statusText)  {
		//redo quantity boxes
		//jQuery("#" + PickUpOrDeliveryModifier.updatedDivID).css("height", "auto");
		jQuery("#" + PickUpOrDeliveryModifier.formID).removeClass(PickUpOrDeliveryModifier.loadingClass);
		AjaxCheckout.setChanges(responseText);
	}
}

