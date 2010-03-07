;(function($) {
	$(document).ready(
		function() {
			AjaxCheckout.redoCartAjax();
			AjaxCheckout.redoCountryQuery();
		}
	);
})(jQuery);

var AjaxCheckout = {

	CartHolderID: "CartHolder",

	updatedDivID: "InformationTable",

	countryQueryDone: false,

	tableTotalID:  "Table_Order_Total",

	loadingHTML: '<span class="loading">calculating...</span>',

	quantitySelector: 'input.ajaxQuantityField',

	quantitySelectorNameEnd: '_SetQuantityLink',

	countrySelector: 'select.ajaxCountryField',

	countrySelectorNameEnd: '_SetCountryLink',

	amountReadOnlySelector: "#Amount span.readonly",

	amountHiddenSelector: "#Amount input",

	redoCartAjax: function() {

		jQuery(AjaxCheckout.quantitySelector).each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						var name = jQuery(this).attr('name') + AjaxCheckout.quantitySelectorNameEnd;
						var setQuantityLink = jQuery('[name=' + name + ']');
						if(jQuery(setQuantityLink).length > 0) {
							setQuantityLink = jQuery(setQuantityLink).get(0);
							this.value = parseInt(this.value);
							if(! this.value || this.value == 'undefined') {
								this.value = 0;
							}
							var url = jQuery('base').attr('href') + setQuantityLink.value + '?quantity=' + this.value;
							AjaxCheckout.startUpdate(url);;
						}
					}
				);
			}
		);
		//update total at the bottom...
	},

	redoCountryQuery: function() {
		jQuery(AjaxCheckout.countrySelector).each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						var id = '#' + jQuery(this).attr('id') + AjaxCheckout.countrySelectorNameEnd;
						var setCountryLink = jQuery(id);
						if(jQuery(setCountryLink).length > 0) {
							setCountryLink = jQuery(setCountryLink).get(0);
							var url = jQuery('base').attr('href') + setCountryLink.value + '/' + this.value;
							AjaxCheckout.startUpdate(url);
						}
					}
				);
			}
		);
	},

	startUpdate: function(url) {
		jQuery("#" + AjaxCheckout.tableTotalID).html(AjaxCheckout.loadingHTML);
		jQuery.getJSON(url, null, AjaxCheckout.setChanges);
	},

	setChanges: function (changes) {
		for(var i in changes) {
			var change = changes[i];
			if(typeof(change.parameter) != 'undefined' && typeof(change.value) != 'undefined') {
				var parameter = change.parameter;
				var value = AjaxCheckout.escapeHTML(change.value);
				if(change.id) {
					var id = '#' + change.id;
					jQuery(id).attr(parameter, value);
				}
				else if(change.name) {
					var name = change.name;
					jQuery('[name=' + name + ']').each(
						function() {
							jQuery(this).attr(parameter, value);
						}
					);
				}
			}
		}
		var total = jQuery("#" + AjaxCheckout.tableTotalID).text();
		jQuery(AjaxCheckout.amountReadOnlySelector).text(total);
		jQuery(AjaxCheckout.amountHiddenSelector).attr("value", total);
	},

	escapeHTML: function (str) {
		var div = document.createElement('div');
		var text = document.createTextNode(str);
		div.appendChild(text);
		return div.innerHTML;
	}

}
