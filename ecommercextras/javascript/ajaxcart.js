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
	},


	updateQuantitySetup: function() {
		$(".ajaxQuantityField").update(
			function() {
				ajaxCart.redoCartAjax();
			}
		);
	},

	redoCartAjax: function() {
		jQuery('input.ajaxQuantityField').each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						var name = jQuery(this).attr('name')+ '_SetQuantityLink';
						var setQuantityLink = jQuery('[name=' + name + ']');
						if(jQuery(setQuantityLink).length > 0) {
							setQuantityLink = jQuery(setQuantityLink).get(0);
							if(! this.value) this.value = 0;
							else this.value = this.value.replace(/[^0-9]+/g, '');
							var url = jQuery('base').attr('href') + setQuantityLink.value + '?quantity=' + this.value;
							jQuery.getJSON(url, null, ajaxCart.redoCartAjax);
						}
					}
				);
			}
		);
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


/*
jQuery(document).ready(
	function() {
		jQuery('input.ajaxQuantityField').each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						var name = jQuery(this).attr('name')+ '_SetQuantityLink';
						var setQuantityLink = jQuery('[name=' + name + ']');
						if(jQuery(setQuantityLink).length > 0) {
							setQuantityLink = jQuery(setQuantityLink).get(0);
							if(! this.value) this.value = 0;
							else this.value = this.value.replace(/[^0-9]+/g, '');
							var url = jQuery('base').attr('href') + setQuantityLink.value + '?quantity=' + this.value;
							jQuery.getJSON(url, null, ajaxCart.redoCartAjax);
						}
					}
				);
			}
		);
		jQuery('select.ajaxCountryField').each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						var id = '#' + jQuery(this).attr('id') + '_SetCountryLink';
						var setCountryLink = jQuery(id);
						if(jQuery(setCountryLink).length > 0) {
							setCountryLink = jQuery(setCountryLink).get(0);
							var url = jQuery('base').attr('href') + setCountryLink.value + '/' + this.value;
							jQuery.getJSON(url, null, setChanges);
						}
					}
				);
			}
		);
	}
);
*/