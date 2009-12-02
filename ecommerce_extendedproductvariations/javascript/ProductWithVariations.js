
/**
 *@author nicolaas[at]sunnysideup . co . nz
 *@description:
 * you can use ActionBeforeSelector / ActionAfterSelector
 * to add extra functionality in your custom mysite code.
 * By adding Requirements::customScript('ProductWithVariations.SetActionAfterSelector("abc")');
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

	loadingClass: "loading",

	inCartClass: "inCart",

	ActionBeforeSelector: "",
		SetActionBeforeSelector: function(v) {this.ActionBeforeSelector = v},

	ActionAfterSelector: "",
		SetActionAfterSelector: function(v) {this.ActionAfterSelector = v},

	init: function() {
		jQuery(this.hiddenCurrentVariationDiv).css("display", "none");
		jQuery(this.dropdownSelector).change(
			function () {
				ProductWithVariations.calculateVariation();
			}
		);
		jQuery(this.dropdownSelector).each(
			function(i) {
				var els = jQuery(this).find("option");
				if(els.length < 2) {
					jQuery(this).parent().parent().hide();
				}
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
		if(ProductWithVariations.ActionBeforeSelector) {
			jQuery(ProductWithVariations.ActionBeforeSelector).click();
		}
		jQuery(ProductWithVariations.formMsgSelector).addClass(ProductWithVariations.loadingClass);
		jQuery(ProductWithVariations.buttonSelector).fadeOut();
    return true;
	},

	showResponse: function (responseText, statusText)  {
		if(ProductWithVariations.ActionAfterSelector) {
			jQuery(ProductWithVariations.ActionAfterSelector).click();
		}
		jQuery(ProductWithVariations.formMsgSelector).removeClass(ProductWithVariations.loadingClass);
		jQuery(ProductWithVariations.formMsgSelector).removeClass(ProductWithVariations.inCartClass);

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


