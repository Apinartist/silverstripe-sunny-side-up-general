/* add and remove using ajax... */

;(function($) {
	$(document).ready(
		function() {
			$("body").addAddLinks();
			$("body").addRemoveLinks();
			$("#"+AjaxOrder.updatedDivID).livequery(
				function() {
					AjaxOrder.redoCartAjax();
				}
			);
			AjaxOrder.redoCountryQuery();
		}
	);
})(jQuery);


var AjaxOrder = {

	CartHolderID: "CartHolder",

	LoadingText: "updating cart ... ",

	InCartText: "In Cart",

	UnconfirmedDelete: false,

	updatedDivID: "InformationTable",

	ConfirmDeleteText: 'Are you sure you would like to remove this item from your cart?',

	countryQueryDone: false,

	tableTotalID:  "Table_Order_Total",

	set_LoadingText: function(v) {
		this.LoadingText = v;
	},

	set_BoughtText: function(v) {
		this.LoadingText = v;
	},

	set_ConfirmDeleteText: function(v) {
		this.ConfirmDeleteText = v;
	},

	loadAjax: function(loadIntoElID, URL, el) {
		jQuery("#" + loadIntoElID).text(AjaxOrder.LoadingText);
		jQuery("#" + loadIntoElID).load(
			URL,
			{},
			function() {
				jQuery(el).text(AjaxOrder.InCartText);
				jQuery("#" + loadIntoElID).addRemoveLinks();
			}
		);
		return true;
	},


	redoCartAjax: function() {

		//quantity updates
		jQuery('input.ajaxQuantityField').each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						AjaxOrder.updateTotals();
						var name = jQuery(this).attr('name')+ '_SetQuantityLink';
						var setQuantityLink = jQuery('[name=' + name + ']');
						if(jQuery(setQuantityLink).length > 0) {
							setQuantityLink = jQuery(setQuantityLink).get(0);
							if(! this.value) this.value = 0;
							else this.value = this.value.replace(/[^0-9]+/g, '');
							var url = jQuery('base').attr('href') + setQuantityLink.value + '?quantity=' + this.value;
							jQuery.getJSON(url, null, AjaxOrder.redoCartAjax);
						}
					}
				);
			}
		);

		//update total at the bottom...
		AjaxOrder.updateTotals();
	},

	updateTotals: function() {
		var total = jQuery("#" + AjaxOrder.tableTotalID).text();
		var total = jQuery("#Amount span.readonly").text(total);
		var total = jQuery("#Amount input").attr("value", total);
	},

	redoCountryQuery: function() {
		// country
		if(!AjaxOrder.countryQueryDone) {
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
			AjaxOrder.countryQueryDone = true;
		}
	}
}






jQuery.fn.extend({

	addAddLinks: function() {
		jQuery(this).find(".ajaxAdd").click(
			function(el){
				var url = jQuery(this).attr("href");
				jQuery(this).text(AjaxOrder.LoadingText);
				AjaxOrder.loadAjax('CartHolder', url, this);
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
	}

});


function setChanges(changes) {
	for(var i in changes) {
		var change = changes[i];
		if(typeof(change.parameter) != 'undefined' && typeof(change.value) != 'undefined') {
			var parameter = change.parameter;
			var value = escapeHTML(change.value);
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
}

function escapeHTML(str) {
   var div = document.createElement('div');
   var text = document.createTextNode(str);
   div.appendChild(text);
   return div.innerHTML;
}