/**
*@author nicolaas[at]sunnysideup . co . nz
*
**/

(function($){
	$(document).ready(
		function() {
			StockControlPage.init();
		}
	);


})(jQuery);


var StockControlPage = {

	ulSelector: "#StockProductObjects",

	inputSelector: "#StockProductObjects input.updateField",

	feedbackSelector: ".StockObjectsFeedback",

	historyLinkSelector: "#StockProductObjects .history a",

	init: function () {
		jQuery(StockControlPage.inputSelector).change(
			function () {
				var nameValue = jQuery(this).attr("name");
				var nameArray = nameValue.split("/");
				var table = nameArray[0];
				var id = nameArray[1];
				var value = parseInt(jQuery(this).val());
				jQuery.get(
					StockControlPageURL + table + "/" + id + "/?v=" + value,
					{},
					function(data) {
						jQuery(StockControlPage.feedbackSelector).html(data)
					}
				);
			}
		);

		jQuery(StockControlPage.historyLinkSelector).click(
			function() {
				jQuery(this).click( function() {jQuery(this).hide();});
				var identifier = jQuery(this).attr("rel");
				var url = jQuery(this).attr("href");
				jQuery.get(
					url,
					{},
					function(data) {
						jQuery("#"+identifier).html(data);
						alert("history loaded");
					}
				);
				return false;
			}

		);
	}



}


