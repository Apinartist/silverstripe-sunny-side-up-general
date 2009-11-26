
/**
*@author nicolaas[at]sunnysideup . co . nz
*
**/

(function($){

	$(document).ready(
		function() {
			ProductWithVariations.init();
			ProductWithVariations.prepAjaxSubmit();
		}
	);


})(jQuery);



ProductWithVariations = {

	ItemArray: new Array(),

	IDArray: new Array(),

	PriceArray: new Array(),

	formSelector: "#Form_ProductVariationsForm",

	formMsgSelector: "#ExtendedProductVariationPrice",

	dropdownSelector: "#ExtendedProductVariationDropdowns select",

	priceSelector: "#ExtendedProductVariationPrice",

	hiddenCurrentVariationDiv: "#CurrentVariation",

	hiddenCurrentVariationSelector: "#Form_ProductVariationsForm_CurrentVariation",

	NotAvailableText: "Sorry, selection is not available",

	init: function() {
		jQuery(this.hiddenCurrentVariationDiv).css("display", "none");
		jQuery(this.dropdownSelector).change(
			function () {
				ProductWithVariations.calculateVariation();
			}
		);
		jQuery(this.dropdownSelector).change();
	},

	calculateVariation: function() {
		var selectedValueArray = new Array();
		var groupIDArray = new Array();
		jQuery(this.dropdownSelector).each(
			function(i) {
				var group = jQuery(this).attr("name");
				var groupIDWithBrackets = group.searchByRegex(new RegExp(/\[\d+\]$/));
				if(groupIDWithBrackets) {
					var groupID = parseInt(groupIDWithBrackets.searchByRegex(new RegExp(/\d+/)));
					if(groupID) {
						selectedValueArray[groupID] = jQuery(this).val();
						groupIDArray[i] = groupID;
					}

				}
			}
		);
		match = false;
		if(selectedValueArray.length) {
			for(i = 0; i < ProductWithVariations.ItemArray.length && false === match; i++) {
				for(j = 0; j<groupIDArray.length;j++){
					if(selectedValueArray[groupIDArray[j]]!=ProductWithVariations.ItemArray[i][groupIDArray[j]]){
						j = 9999;
					}
				}
				if(j < 9999) {
					match = i;
				}
			}
		}
		if(match !== false) {
			jQuery(ProductWithVariations.hiddenCurrentVariationSelector).attr("value",ProductWithVariations.IDArray[match]);
			jQuery(ProductWithVariations.priceSelector).text(ProductWithVariations.PriceArray[match]);
		}
		else {
			jQuery(ProductWithVariations.hiddenCurrentVariationSelector).attr("value",match);
			jQuery(ProductWithVariations.priceSelector).text(ProductWithVariations.NotAvailableText);

		}
	},

	prepAjaxSubmit: function(){
		var options = {
				target:        ProductWithVariations.formMsgSelector,   // target element(s) to be updated with server response
				beforeSubmit:  ProductWithVariations.showRequest,  // pre-submit callback
				success:       ProductWithVariations.showResponse  // post-submit callback
				// other available options:
				//url:       url         // override for form's 'action' attribute
				//type:      type        // 'get' or 'post', override for form's 'method' attribute
				//dataType:  null        // 'xml', 'script', or 'json' (expected server response type)
				//clearForm: true        // clear all form fields after successful submit
				//resetForm: true        // reset the form after successful submit
		};
    jQuery(ProductWithVariations.formSelector).ajaxForm(options);
	},

	showRequest: function (formData, jqForm, options) {
    return true;
	},

	showResponse: function (responseText, statusText)  {
	}

}




String.prototype.searchByRegex = function(RegExpObject) {
  var m = RegExpObject.exec(this);
	if (m == null) {
    return false;
  }
	else {
		s = "";
    for (i = 0; i < m.length; i++) {
      s = s + m[i];
    }
		return s;
  }
}


