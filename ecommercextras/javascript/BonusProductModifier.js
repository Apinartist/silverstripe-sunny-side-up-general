var BonusProductModifierArray = new Array();

(function($){
	$(document).ready(
		function() {
			BonusProductModifier.init();
		}
	);
})(jQuery);


var	BonusProductModifier = {

	list: new Array(),

	setList: function(arrayValues) {
		this.list = arrayValues;
	},

	init: function() {
		this.setList(BonusProductModifierArray);
		if(this.list.length > 0) {
			for(var i = 0; i <= this.list.length; i++) {
				if(this.list[i] > 0) {
					var trID = "#Table_Product_OrderItem_"+this.list[i];
					jQuery(trID).addClass("bonusproduct");
					jQuery(trID + " td.remove").html("&nbsp;");
					var t = setTimeout(function() {jQuery(trID+" input.ajaxQuantityField").attr("disabled", true).attr("disabled", "disabled"); t = null},500)
				}
			}
		}
	}
}