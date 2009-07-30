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

	updatedDivID: "OrderInformationEditable",

	actionsClass: "Actions",

	loadingText: '<p class="loading">loading...</p>',

	init: function() {
		var options = {
			target:        '#' + PickUpOrDeliveryModifier.updatedDivID,   // target element(s) to be updated with server response
			beforeSubmit:  PickUpOrDeliveryModifier.showRequest,  // pre-submit callback
			success:       PickUpOrDeliveryModifier.showResponse  // post-submit callback
		};
		jQuery('#' + PickUpOrDeliveryModifier.formID).ajaxForm(options);
		jQuery("#" + PickUpOrDeliveryModifier.formID + " ." + PickUpOrDeliveryModifier.actionsClass).hide();
		jQuery("#" + PickUpOrDeliveryModifier.formID+ PickUpOrDeliveryModifier.dropDownIDappendix).change(
			function() {
				var height = jQuery("#" + PickUpOrDeliveryModifier.updatedDivID).height();
				jQuery("#" + PickUpOrDeliveryModifier.updatedDivID).html(PickUpOrDeliveryModifier.loadingText).css("height", height+"px");
				jQuery("#" + PickUpOrDeliveryModifier.formID).submit();
			}
		);
	},

	// pre-submit callback
	showRequest: function (formData, jqForm, options) {
			return true;
	},

	// post-submit callback
	showResponse: function (responseText, statusText)  {
		//redo quantity boxes
		jQuery("#" + PickUpOrDeliveryModifier.updatedDivID).css("height", "auto");
	}
}

