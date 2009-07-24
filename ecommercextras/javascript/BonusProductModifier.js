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

	subTotalTdID: "Table_Order_SubTotal",

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
			var subTotal = BonusProductModifier.makeFloatFromString(jQuery("#" + BonusProductModifier.subTotalTdID).text());
			var newSubTotal = subTotal;
			if(BonusProductModifierSaving > 0 && BonusProductModifierSaving < subTotal && subTotal > 0) {
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
		BonusProductModifier.currencySign = v.substring(0,1);
		var substring = v.substring(1,length);
		var floatValue = parseFloat(substring.replace(",", ""));
		if(floatValue > 0) {
			return floatValue;
		}
		else {
			alert("could not calculate savings");
		}
	},

	makeCurrencyfromFloat: function(v) {
		return BonusProductModifier.currencySign + v.formatMoney(2, ".", ",");
	},


}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

