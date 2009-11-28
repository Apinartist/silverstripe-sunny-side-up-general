/**
 *@author Nicolaas [at] sunnysideup.co.nz
 *@description: adds ajax functionality to page
 *we have three options:
 * * addLinks (click to add to cart)
 * * delete links (remove from cart)
 * * and remove from cart (cart is expected to be as <li>......<a href="">click to remove</a>, with a tag being a direct child of li, and li holding item
 **/

;(function($) {
	$(document).ready(
		function() {
			AjaxCart.init("body");
		}
	);
})(jQuery);

var AjaxCart = {

	ConfirmDeleteText: 'Are you sure you would like to remove this item from your cart?',

	LoadingText: "updating cart ... ",

	LoadingClass: "loading",

	ajaxAddRemoveLinkSelector: ".ajaxAddToCartLink",

	showSelector: ".show",

	doNotShowSelector: ".doNotShow",

	addLinkSelector: ".ajaxAdd",

	removeLinkSelector: ".ajaxRemove",

	removeCartSelector: ".removeFromCart",

	InCartText: "In Cart",

	cartHolderSelector: "#CartHolder",

	UnconfirmedDelete: false,

	init: function(element) {
		$(element).addAddLinks();
		$(element).addRemoveLinks();
		$(element).addCartRemove();
	},

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
		jQuery(el).addClass(AjaxCart.LoadingClass);
		var clickedElement = el;
		jQuery.get(
			url,
			{},
			function(data) {
				alert("doing it");
				jQuery(AjaxCart.cartHolderSelector).html(data);
				jQuery(clickedElement).removeClass(AjaxCart.LoadingClass);
				jQuery(clickedElement).parent(AjaxCart.ajaxAddRemoveLinkSelector).find(AjaxCart.showSelector).removeClass(AjaxCart.showSelector).addClass(AjaxCart.doNotShowSelector);
				jQuery(clickedElement).parent(AjaxCart.ajaxAddRemoveLinkSelector).find(AjaxCart.doNotShowSelector).removeClass(AjaxCart.doNotShowSelector).addClass(AjaxCart.showSelector);
				AjaxCart.init(clickedElement);
			}
		);
		return true;
	}




}


jQuery.fn.extend({

	addAddLinks: function() {
		jQuery(this).find(AjaxCart.addLinkSelector).click(
			function(){
				var url = jQuery(this).attr("href");
				jQuery(this).text(AjaxCart.LoadingText);
				AjaxCart.loadAjax(url, this);
				return false;
			}
		);
	},

	addCartRemove: function () {
		jQuery(this).find(AjaxCart.removeCartSelector).click(
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

	addRemoveLinks: function () {
		jQuery(this).find(AjaxCart.removeLinkSelector).click(
			function(){
				var url = jQuery(this).attr("href");
				AjaxCart.loadAjax(url, this);
				return false;
			}
		);
	}

});

