/* add and remove using ajax... */

;(function($) {
	$(document).ready(
		function() {
			$("body").addAddLinks();
			$("body").addRemoveLinks();
		}
	);
})(jQuery);

var AjaxCart = {

	ConfirmDeleteText: 'Are you sure you would like to remove this item from your cart?',

	LoadingText: "updating cart ... ",

	LoadingClass: "loading ... ",

	InCartText: "In Cart",

	cartHolderSelector: "#CartHolder",

	UnconfirmedDelete: false,

	set_LoadingText: function(v) {
		this.LoadingText = v;
	},

	set_InCartText: function(v) {
		this.InCartText = v;
	},

	set_ConfirmDeleteText: function(v) {
		this.ConfirmDeleteText = v;
	},

	loadAjax: function( url, el ) {
		jQuery(AjaxCart.cartHolderSelector).html('<span class="'+AjaxCart.LoadingClass+'">'+AjaxCart.LoadingText+'</span>');
		jQuery(AjaxCart.cartHolderSelector).load(
			url,
			{},
			function() {
				jQuery(el).text(AjaxCart.InCartText);
				jQuery(this).addRemoveLinks();
			}
		);
		return true;
	}

}


jQuery.fn.extend({

	addAddLinks: function() {
		jQuery(this).find(".ajaxAdd").click(
			function(){
				var url = jQuery(this).attr("href");
				jQuery(this).text(AjaxCart.LoadingText);
				AjaxCart.loadAjax(url, this);
				return false;
			}
		);
	},

	addRemoveLinks: function () {
		jQuery(this).find(".removeLink").click(
			function(){
				if(AjaxCart.UnconfirmedDelete || confirm(AjaxCart.ConfirmDeleteText)) {
					var url = jQuery(this).attr("href");
					var el = this;//we need this to retain link to element (this shifts focus)
					jQuery(el).parent("li").css("text-decoration", "line-through");
					jQuery.get(url, function(){ jQuery(el).parent("li").fadeOut();});
				}
				return false;
			}
		);
	},

});

