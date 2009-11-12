
/**
*@author nicolaas[at]sunnysideup . co . nz
*
**/

(function($){

	$(document).ready(
		function() {
			ProductWithVariations.init();
		}
	);


})(jQuery);



ProductWithVariations = {

	ItemArray: new Array(),

	IDArray: new Array(),

	PriceArray: new Array(),

	dropdownSelector: "#ExtendedProductVariationDropdowns select",

	priceSelector: "#ExtendedProductVariationPrice",

	hiddenCurrentVariationSelector: "#Form_ProductVariationsForm_CurrentVariation",

	NotAvailableText: "Sorry, selection is not available",

	init: function() {
		jQuery(this.hiddenCurrentVariationSelector).attr("disabled", "disabled");
		jQuery(this.dropdownSelector).change(
			function () {
				ProductWithVariations.calculateVariation();
			}
		);
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


