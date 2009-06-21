/* add and remove using ajax... */

;(function($) {
	$(document).ready(
		function() {
			$("body").addAddLinks();
			$("body").addRemoveLinks();
		}
	);
})(jQuery);


var ajaxCart = {

	CartHolderID: "CartHolder",

	LoadingText: "updating cart ... ",

	BoughText: "Bought",

	UnconfirmedDelete: false,

	ConfirmText: 'Are you sure you would like to remove this item from your cart?',

	loadAjax: function(loadIntoElID, URL, el) {
		jQuery("#" + loadIntoElID).text(ajaxCart.LoadingText);
		jQuery("#" + loadIntoElID).load(
			URL,
			{},
			function() {
				jQuery(el).text(ajaxCart.BoughText);
				jQuery("#" + loadIntoElID).addRemoveLinks();
			}
		);
		return true;
	}

}

jQuery.fn.extend({

	addAddLinks: function() {
		jQuery(this).find(".ajaxAdd").click(
			function(el){
				var url = jQuery(this).attr("href");
				jQuery(this).text(ajaxCart.LoadingText);
				ajaxCart.loadAjax('CartHolder', url, this);
				return false;
			}
		);
	},

	addRemoveLinks: function () {
		jQuery(this).find(".removeLink").click(
			function(){
				if(ajaxCart.UnconfirmedDelete || confirm(ajaxCart.ConfirmText)) {
					var url = jQuery(this).attr("href");
					var el = this;
					jQuery(el).parent("li").css("text-decoration", "line-through");
					jQuery.get(url, function(){ jQuery(el).parent("li").fadeOut();});
				}
				return false;
			}
		);
	}

});
