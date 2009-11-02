(function($){
	$(document).ready(
		function() {
			PickUpOrDeliveryModifier.init();
		}
	);
})(jQuery);


var	PickUpOrDeliveryModifier = {

	formID: "PickUpOrDeliveryModifier_Form_ModifierForm",

	DropdownIDappendix: "_PickupOrDeliveryType",

	loadingClass: "loading",

	actionsClass: "Actions",

	countryDropdownSelector: "select.ajaxCountryField",

	notSelectedText: "-- not selected --",

	availableCountries: new Array(),

	init: function() {
		var options = {
			beforeSubmit:  PickUpOrDeliveryModifier.showRequest,  // pre-submit callback
			success: PickUpOrDeliveryModifier.showResponse,  // post-submit callback
			dataType: "json"
		};
		jQuery('#' + PickUpOrDeliveryModifier.formID).ajaxForm(options);
		jQuery("#" + PickUpOrDeliveryModifier.formID + " ." + PickUpOrDeliveryModifier.actionsClass).hide();
		PickUpOrDeliveryModifier.updateCountryList();
		jQuery("#" + PickUpOrDeliveryModifier.formID+ PickUpOrDeliveryModifier.DropdownIDappendix).change(
			function() {
				PickUpOrDeliveryModifier.updateCountryList();
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
	},

	addAvailableCountriesItem: function(index, countriesArray) {
		PickUpOrDeliveryModifier.availableCountries[index] = countriesArray;
	},

	updateCountryList: function() {
		var currentIndex = jQuery("#" + PickUpOrDeliveryModifier.formID+ PickUpOrDeliveryModifier.DropdownIDappendix).val();
		var acceptableOptions = PickUpOrDeliveryModifier.availableCountries[currentIndex];
		if(acceptableOptions.length < 1) {
			jQuery(PickUpOrDeliveryModifier.countryDropdownSelector + " option").show();
		}
		else {
			jQuery(PickUpOrDeliveryModifier.countryDropdownSelector + " option").hide();
		}
		var hasValidValue = false;
		for(i=0;i<acceptableOptions.length;i++) {
			jQuery(PickUpOrDeliveryModifier.countryDropdownSelector + " option[value='" + acceptableOptions[i] + "']").show();
			if(jQuery(PickUpOrDeliveryModifier.countryDropdownSelector).val() == acceptableOptions[i]) {
				hasValidValue = true;
			}
		}
		if(acceptableOptions.length == 1) {
			jQuery(PickUpOrDeliveryModifier.countryDropdownSelector).val(acceptableOptions[i]);
			hasValidValue = true;
		}
		if(hasValidValue) {
			jQuery(PickUpOrDeliveryModifier.countryDropdownSelector + " option.nothingSelected").hide();
		}
		else {
			if(jQuery(PickUpOrDeliveryModifier.countryDropdownSelector + " option.nothingSelected").length < 1) {
				jQuery(PickUpOrDeliveryModifier.countryDropdownSelector).prepend('<option class="nothingSelected" value="-">'+PickUpOrDeliveryModifier.notSelectedText+'</option>');
			}
			else {
				jQuery(PickUpOrDeliveryModifier.countryDropdownSelector + " option.nothingSelected").show();
			}
			jQuery(PickUpOrDeliveryModifier.countryDropdownSelector).val("-");
		}
		jQuery(PickUpOrDeliveryModifier.countryDropdownSelector).change();
	}


}

