(function($){
	$(document).ready(
		function() {
			BonusProductModifier.init();
			$("." + BonusProductModifier.addedClassName).livequery(
				function () {
					BonusProductModifier.init();
				}
			);
		}
	);
})(jQuery);


var	BonusProductModifier = {

	list: new Array(),

	order_item_classname: "Product_OrderItem",

	preIDstring: "Table_",

	addedClassName: "bonusproduct",

	set_list: function(arrayVariable) {
		this.list = arrayVariable;
	},

	set_order_item_classname: function(stringValue) {
		this.order_item_classname = stringValue;
	},

	items: new Array(),

	init: function() {
		if(this.list.length > 0) {
			for(var i = 0; i <= this.list.length; i++) {
				if(this.list[i] > 0) {
					var trID = "#"+ this.preIDstring + this.order_item_classname + "_" + this.list[i];
					jQuery(trID).addClass(BonusProductModifier.addedClassName);
					jQuery(trID+" input").hide();
					jQuery(trID+" td.remove").html("&nbsp;");
				}
			}
		}
	}
}

