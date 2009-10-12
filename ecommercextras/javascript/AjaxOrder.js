/* add and remove using ajax... */

;(function($) {
	$(document).ready(
		function() {
			$("body").addAddLinks();
			$("body").addRemoveLinks();
		}
	);
})(jQuery);

var AjaxOrder {

	ConfirmDeleteText: 'Are you sure you would like to remove this item from your cart?',

	LoadingText: "updating cart ... ",

	InCartText: "In Cart",

	UnconfirmedDelete: false,

	set_LoadingText: function(v) {
		this.LoadingText = v;
	},

	set_InCartText: function(v) {
		this.InCartText = v;
	},

	set_ConfirmDeleteText: function(v) {
		this.ConfirmDeleteText = v;
	}
}


jQuery.fn.extend({

	addAddLinks: function() {
		jQuery(this).find(".ajaxAdd").click(
			function(el){
				var url = jQuery(this).attr("href");
				jQuery(this).text(AjaxOrder.LoadingText);
				$("#CartHolder").loadAjax(url, this);
				return false;
			}
		);
	},

	addRemoveLinks: function () {
		jQuery(this).find(".removeLink").click(
			function(){
				if(AjaxOrder.UnconfirmedDelete || confirm(AjaxOrder.ConfirmDeleteText)) {
					var url = jQuery(this).attr("href");
					var el = this;
					jQuery(el).parent("li").css("text-decoration", "line-through");
					jQuery.get(url, function(){ jQuery(el).parent("li").fadeOut();});
				}
				return false;
			}
		);
	},

	loadAjax: function( URL, el) {
		jQuery(this).text(AjaxOrder.LoadingText);
		jQuery(this).load(
			URL,
			{},
			function() {
				jQuery(el).text(AjaxOrder.InCartText);
				jQuery(this).addRemoveLinks();
			}
		);
		return true;
	}

});

