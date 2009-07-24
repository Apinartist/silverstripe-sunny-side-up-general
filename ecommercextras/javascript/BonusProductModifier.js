var BonusProductModifierArray = new Array();

(function($){
	$(document).ready(
		function() {
			$(".bonusproductmodifier").livequery(
				function () {
					if(1 == BonusProductModifier.version) {
						BonusProductModifier.init();
					}
					else {
						BonusProductModifier.alternativeInit();
					}
				}
			);
		}
	);
})(jQuery);


var	BonusProductModifier = {

	list: new Array(),

	setList: function(arrayValues) {
		this.list = arrayValues;
	},

	description: "Bonus Products Savings",

	summaryTrClass: "bonusproductmodifier",

	currencySign: "$",

	version: 1,

	items: new Array(),

	init: function() {
		this.setList(BonusProductModifierArray);
		if(this.list.length > 0) {
			for(var i = 0; i <= this.list.length; i++) {
				if(this.list[i] > 0) {
					var trID = "#Table_Product_OrderItem_"+this.list[i];
					jQuery(trID).each (
						function() {
							var content = jQuery(this).contents().filter("td:first").html();
							if(content && content != "undefined") {
								BonusProductModifier.items[i] = content;
							}
							jQuery(this).hide();
						}
					);
				}
			}
		}
		if(BonusProductModifierArray.length > 0) {
			var html = BonusProductModifier.description;
			html += "<ul>";
			for(var i = 0; i <= BonusProductModifier.items.length; i++) {
				if(BonusProductModifier.items[i] != "undefined" && BonusProductModifier.items[i]) {
					html += "<li>" + BonusProductModifier.items[i] + "</li>";
				}
			}
			html += "</ul>";
			jQuery("." + BonusProductModifier.summaryTrClass + " td:first").html(html);
			var subTotal = BonusProductModifier.makeFloatFromString(jQuery("#Table_Order_SubTotal").text());
			var newSubTotal = subTotal;
			if(BonusProductModifierSaving > 0 && BonusProductModifierSaving < subTotal) {
				newSubTotal = subTotal - BonusProductModifierSaving;
			}

			var value = jQuery("#Table_Order_SubTotal").text(BonusProductModifier.makeStringfromFloat(newSubTotal));

		}
	},

	alternativeInit: function() {
		this.setList(BonusProductModifierArray);
		if(this.list.length > 0) {
			for(var i = 0; i <= this.list.length; i++) {
				if(this.list[i] > 0) {
					var trID = "#Table_Product_OrderItem_"+this.list[i];
					jQuery(trID).each (
						function() {
							jQuery(this).contents().filter("td:first").text("$0.00");
						}
					);
				}
			}
		}
		if(BonusProductModifierArray.length > 0) {
			var html = BonusProductModifier.description;
			html += "<ul>";
			for(var i = 0; i <= BonusProductModifier.items.length; i++) {
				if(BonusProductModifier.items[i] != "undefined" && BonusProductModifier.items[i]) {
					html += "<li>" + BonusProductModifier.items[i] + "</li>";
				}
			}
			html += "</ul>";
			jQuery(".bonusproductmodifier td:first").html(html);
		}
	},

	makeFloatFromString: function(v) {
		var length = v.length;
		BonusProductModifier.currencySign = str.substring(0,1)
		var float = floatVal(str.substring(1,length - 1));
		if(float > 0) {
			return float;
		}
	},

	makeStringfromFloat: function(v) {
		return BonusProductModifier.currencySign + v;
	}
}

