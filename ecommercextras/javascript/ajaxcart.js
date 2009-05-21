/* add and remove using ajax... */

;(function($) {
	$(document).ready(function() {
		ajaxCart.addAddLinks();
	});

	var ajaxCart = {

		CartHolderID: "CartHolder",

		LoadingText: "updating cart ... ",

		BoughText: "Bought",

		addAddLinks: function() {
			$(".ajaxAdd").click(
				function(){
					var url = $(this).attr("href");
					$(this).text(ajaxCart.LoadingText);
					ajaxCart.loadAjax('CartHolder', url, this);
					return false;
				}
			);

		},

		loadAjax: function(loadIntoElID, URL, el) {
			$("#" + loadIntoElID).text(ajaxCart.LoadingText);
			$("#" + loadIntoElID).load(URL, {}, function() {$(el).text(ajaxCart.BoughText);});
			return true;
		}
	}

})(jQuery);