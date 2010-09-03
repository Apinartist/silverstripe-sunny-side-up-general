/**
 *@author Nicolaas [at] sunnysideup.co.nz
 *@description: makes the add to wish list links ajax.
 **/

;(function($) {
	$(document).ready(
		function() {
			EcommerceWishList.init(".addToWishListHolder");
		}
	);
})(jQuery);

var EcommerceWishList = {

	ConfirmDeleteText: 'Are you sure you would like to remove this item from your wish list?',

	LoadingClass: "loading",

	showClass: "show",

	doNotShowClass: "doNotShow",

	addLinkSelector: ".addToWishListLink",

	removeLinkSelector: ".removeToWishListLink",

	UnconfirmedDelete: false,

	init: function(element) {
		jQuery(element).addWishListAddLinks();
		jQuery(element).addWishListRemoveLinks();
	},


	set_ConfirmDeleteText: function(v) {
		this.ConfirmDeleteText = v;
	},

	loadAjax: function( url, el ) {
		var clickedElement = el;
		jQuery.get(
			url,
			{},
			function(data) {
				jQuery(el).text(data);
				jQuery(this).removeClass(EcommerceWishList.LoadingClass);
			}
		);
		return true;
	}




}


jQuery.fn.extend({

	addWishListAddLinks: function() {
		jQuery(this).find(EcommerceWishList.addLinkSelector).click(
			function(){
				jQuery(this).addClass(EcommerceWishList.LoadingClass);
				var url = jQuery(this).attr("href");
				EcommerceWishList.loadAjax(url, this);
				return false;
			}
		);
	},

	addWishListRemoveLinks: function () {
		jQuery(this).find(EcommerceWishList.removeLinkSelector).click(
			function(){
				if(EcommerceWishList.UnconfirmedDelete || confirm(EcommerceWishList.ConfirmDeleteText)) {
					jQuery(this).addClass(EcommerceWishList.LoadingClass);
					var url = jQuery(this).attr("href");
					EcommerceWishList.loadAjax(url, this);
				}
				return false;
			}
		);
	}

});

