
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

	returnMessageSelector: "#ExtendedProductVariationMessage",

	buttonSelector: ".Actions input",

	dropdownSelector: "#ExtendedProductVariationDropdowns select",

	priceSelector: "#ExtendedProductVariationPrice",

	hiddenCurrentVariationDiv: "#CurrentVariation",

	hiddenCurrentVariationSelector: "#CurrentVariation select",

	NotAvailableText: "Sorry, selection is not available",

	AddedToCartText: "Add Again",
		SetAddedToCartText: function(v) {ProductWithVariations.AddedToCartText = v},

	AddToCartText: "Add to Cart",
		SetAddToCartText: function(v) {ProductWithVariations.AddToCartText = v},

	InCartPriceAddition: " (in cart)",
		SetInCartPriceAddition: function(v) {ProductWithVariations.InCartPriceAddition = v},

	addedProducts: new Array(),
		AddProduct: function(id) {ProductWithVariations.addedProducts[ProductWithVariations.addedProducts.length] = id;},

	toBeAddedClass: "toBeAdded",

	loadingClass: "loading",

	inCartClass: "inCart",

	checkoutLinkSelector: ".goToCheckout",

	checkoutLinkShowClass: "goToCheckoutShow",

	checkoutLinkHideClass: "goToCheckoutHide",

	ActionBeforeSelector: "",
		SetActionBeforeSelector: function(v) {ProductWithVariations.ActionBeforeSelector = v},

	ActionAfterSelector: "",
		SetActionAfterSelector: function(v) {ProductWithVariations.ActionAfterSelector = v},

	init: function() {
		jQuery(ProductWithVariations.hiddenCurrentVariationDiv).css("display", "none");
		jQuery(ProductWithVariations.dropdownSelector).change(
			function () {
				ProductWithVariations.calculateVariation(false);
			}
		);
		jQuery(ProductWithVariations.dropdownSelector).each(
			function(i) {
				var els = jQuery(this).find("option");
				if(els.length < 2) {
					jQuery(this).parent().parent().hide();
				}
			}
		);
		ProductWithVariations.calculateVariation(true);
	},

	calculateVariation: function(initial) {
		var selectedValueArray = new Array();
		//work out selected array
		var groupIDArray = new Array();
		jQuery(ProductWithVariations.dropdownSelector).each(
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
		// find match of selection with data
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
		//setup situation depending on whether item is in cart or not
		var situationType = "add"
		if(ProductWithVariations.isInCart(ProductWithVariations.IDArray[match])) {
			situationType = "added";
		}
		ProductWithVariations.setupSituation(situationType, match, initial);
	},

	prepAjaxSubmit: function(){
		var options = {
			target:        ProductWithVariations.returnMessageSelector,
			beforeSubmit:  ProductWithVariations.showRequest,
			success:       ProductWithVariations.showResponse,
			type:          "post"
			//dataType:      "xml"
			//url:         url
			//clearForm:   true
			//resetForm:   true
		};
    jQuery(ProductWithVariations.formSelector).ajaxForm(options);
	},

	showRequest: function (formData, jqForm, options) {
		// for extensions
		if(ProductWithVariations.ActionBeforeSelector) {
			jQuery(ProductWithVariations.ActionBeforeSelector).click();
		}
		//add product to list of added products
		ProductWithVariations.AddProduct(jQuery(ProductWithVariations.hiddenCurrentVariationSelector).val());
		//hide button
		ProductWithVariations.setupSituation("loading", false, false);
    return true;
	},

	showResponse: function (responseText, statusText)  {
		//for extensions
		if(ProductWithVariations.ActionAfterSelector) {
			jQuery(ProductWithVariations.ActionAfterSelector).click();
		}
		jQuery(ProductWithVariations.returnMessageSelector).html(responseText);
		//bring back messages
		var match = ProductWithVariations.getMatchFromID(jQuery(ProductWithVariations.hiddenCurrentVariationSelector).val());
		ProductWithVariations.setupSituation("added", match , false);
	},

	setupSituation: function(situationType, match, initial) {//situationType = add / loading / added
		var hideButton = true;
		if(situationType == "add" || situationType == "added") {
			hideButton = false;
			jQuery(ProductWithVariations.returnMessageSelector).slideUp();
			if(match !== false) {
				//show all details for selected entry
				jQuery(ProductWithVariations.hiddenCurrentVariationSelector).attr("value",ProductWithVariations.IDArray[match]);
				if(ProductWithVariations.onlyOneOption()) {
					var priceText = ProductWithVariations.PriceArray[0];
				}
				else {
					var priceText = ProductWithVariations.PriceArray[match]
				}
				if(!priceText) {
					priceText = ProductWithVariations.PriceArray[0];
				}
				if(situationType == "added") {
					priceText += ProductWithVariations.InCartPriceAddition;
				}
				jQuery(ProductWithVariations.priceSelector).text(priceText);
			}
			else {
				if(!initial) {
					jQuery(ProductWithVariations.hiddenCurrentVariationSelector).attr("value",match);
					jQuery(ProductWithVariations.priceSelector).text(ProductWithVariations.NotAvailableText);
					hideButton = true;
				}
				if(!ProductWithVariations.onlyOneOption()) {
					hideButton = true;
				}
			}
		}
		if(situationType == "add") {
			jQuery(ProductWithVariations.buttonSelector).attr("value", ProductWithVariations.AddToCartText);
			jQuery(ProductWithVariations.formMsgSelector).removeClass(ProductWithVariations.loadingClass).removeClass(ProductWithVariations.inCartClass).addClass(ProductWithVariations.toBeAddedClass);
		}
		else if(situationType == "added") {
			//button
			jQuery(ProductWithVariations.buttonSelector).attr("value", ProductWithVariations.AddedToCartText);
			//feedback message
			jQuery(ProductWithVariations.returnMessageSelector).slideDown();
			//message
			jQuery(ProductWithVariations.formMsgSelector).removeClass(ProductWithVariations.loadingClass).removeClass(ProductWithVariations.toBeAddedClass).addClass(ProductWithVariations.inCartClass);
			//we keep this - do not remove!
			jQuery(ProductWithVariations.checkoutLinkSelector).removeClass(ProductWithVariations.checkoutLinkHideClass).addClass(ProductWithVariations.checkoutLinkShowClass);
		}
		else if(situationType = "loading") {
			//feedback message
			jQuery(ProductWithVariations.returnMessageSelector).slideUp();
			//message
			jQuery(ProductWithVariations.formMsgSelector).removeClass(ProductWithVariations.inCartClass).removeClass(ProductWithVariations.toBeAdded).addClass(ProductWithVariations.loadingClass).text(situationType);
		}
		if(hideButton) {
			jQuery(ProductWithVariations.buttonSelector).slideUp();
		}
		else {
			jQuery(ProductWithVariations.buttonSelector).slideDown();
		}
	},

	getMatchFromID: function (val) {
		if(val == -1) {
			return val;
		}
		for(i = 0; i < ProductWithVariations.IDArray.length; i++) {
			if(ProductWithVariations.IDArray[i] == val) {
				return i;
			}
		}
		return false;
	},

	onlyOneOption: function() {
		return jQuery(ProductWithVariations.formSelector+" option").length == 1;
	},

	isInCart: function (itemID) {
		if(ProductWithVariations.addedProducts.length > 0 && ProductWithVariations.onlyOneOption()) {
			return true;
		}
		for(i = 0; i < ProductWithVariations.addedProducts.length; i++) {
			if (ProductWithVariations.addedProducts[i] == itemID) {
				return true;
			}
		}
		return false;
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


