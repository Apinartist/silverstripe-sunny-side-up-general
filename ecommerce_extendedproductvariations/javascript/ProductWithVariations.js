
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

	buttonSelector: ".Actions input",

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
			jQuery(ProductWithVariations.buttonSelector).slideDown();
		}
		else {
			jQuery(ProductWithVariations.hiddenCurrentVariationSelector).attr("value",match);
			jQuery(ProductWithVariations.priceSelector).text(ProductWithVariations.NotAvailableText);
			jQuery(ProductWithVariations.buttonSelector).slideUp();

		}
	},

	prepAjaxSubmit: function(){
		var options = {
			target:        ProductWithVariations.formMsgSelector,
			beforeSubmit:  ProductWithVariations.showRequest,
			success:       ProductWithVariations.showResponse,
			type:          "post",
			//dataType:      "xml"
			//url:         url
			//clearForm:   true
			//resetForm:   true
		};
    jQuery(ProductWithVariations.formSelector).ajaxForm(options);
	},

	showRequest: function (formData, jqForm, options) {
		jQuery(ProductWithVariations.formMsgSelector).addClass("loading");
    return true;
	},

	showResponse: function (responseText, statusText)  {
		jQuery(ProductWithVariations.formMsgSelector).removeClass("loading");
		//jQuery(AjaxCart.cartHolderSelector).html(data);
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


