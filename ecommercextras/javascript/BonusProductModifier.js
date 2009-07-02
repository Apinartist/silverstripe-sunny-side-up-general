var BonusProductModifierArray = new Array();

(function($){
	$(document).ready(
		function() {
			$(".bonusproductmodifier").livequery(
				function () {
					BonusProductModifier.init();
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
			jQuery(".bonusproductmodifier td:first").html(html);
		}
	}
}

