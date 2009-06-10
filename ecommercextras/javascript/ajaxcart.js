/* add and remove using ajax... */

;(function($) {
	$(document).ready(function() {
		$("body").addAddLinks();
		$("body").addRemoveLinks();
		alert("test");
	});
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
				$(el).text(ajaxCart.BoughText);
				$("#" + loadIntoElID).addRemoveLinks();
			}
		);
		return true;
	}

}

jQuery.fn.extend({

	addAddLinks: function() {
		$(this).find(".ajaxAdd").click(
			function(el){
				var url = jQuery(this).attr("href");
				jQuery(this).text(ajaxCart.LoadingText);
				ajaxCart.loadAjax('CartHolder', url, this);
				return false;
			}
		);
	},

	addRemoveLinks: function () {
		$(this).find(".removeLink").click(
			function(){
				if(ajaxCart.UnconfirmedDelete || confirm(ajaxCart.ConfirmText)) {
					var url = $(this).attr("href");
					var el = this;
					jQuery(el).parent("li").css("text-decoration", "line-through");
					jQuery.get(url, function(){ $(el).parent("li").fadeOut();});
				}
				return false;
			}
		);
	}

});
