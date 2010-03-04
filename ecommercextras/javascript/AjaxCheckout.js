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

	emailFieldSelector: "input[name='Email']",

	emailFieldError: "Please check your email address.",

	emailFieldCheckingMessage: "checking email address",

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

	LiveEmailCheckModifier: function () {
		jQuery(AjaxCheckout.emailFieldSelector).change(
			function () {
				var email = jQuery(this).val();
				jQuery(this).addClass("loading");
				jQuery(this).val(AjaxCheckout.emailFieldCheckingMessage);
				var base = jQuery("base").attr("href");
				url = base + "liveemailcheckmodifier/checkemail/?email=" + email;
				jQuery.get(
					url,
					function(response) {
						jQuery(AjaxCheckout.emailFieldSelector).val(email);
						jQuery(AjaxCheckout.emailFieldSelector).removeClass("loading");
						if(response != "ok") {
							if(response == "invalid") {
								alert(AjaxCheckout.emailFieldError);
								jQuery(AjaxCheckout.emailFieldSelector).focus();
							}
							else {
								if(confirm(response)) {
									window.location = base + "Security/login/?BackURL=" + escape(window.location);
								}
								else {
									jQuery(AjaxCheckout.emailFieldSelector).focus();
								}
							}
						}
					}
				);

			}
		);

	},

	escapeHTML: function (str) {
		var div = document.createElement('div');
		var text = document.createTextNode(str);
		div.appendChild(text);
		return div.innerHTML;
	}

}
