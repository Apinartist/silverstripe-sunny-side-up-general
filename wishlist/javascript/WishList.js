/**
 *@author Nicolaas [at] sunnysideup.co.nz
 *@description: makes the add to wish list links ajax.
 **/

;(function($) {
	$(document).ready(
		function() {
			WishList.init(".addToWishListHolder");
		}
	);
})(jQuery);

var WishList = {

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
				jQuery(this).removeClass(WishList.LoadingClass);
			}
		);
		return true;
	}




}


jQuery.fn.extend({

	addWishListAddLinks: function() {
		jQuery(this).find(WishList.addLinkSelector).click(
			function(){
				jQuery(this).addClass(WishList.LoadingClass);
				var url = jQuery(this).attr("href");
				WishList.loadAjax(url, this);
				return false;
			}
		);
	},

	addWishListRemoveLinks: function () {
		jQuery(this).find(WishList.removeLinkSelector).click(
			function(){
				if(WishList.UnconfirmedDelete || confirm(WishList.ConfirmDeleteText)) {
					jQuery(this).addClass(WishList.LoadingClass);
					var url = jQuery(this).attr("href");
					WishList.loadAjax(url, this);
				}
				return false;
			}
		);
	}

});

