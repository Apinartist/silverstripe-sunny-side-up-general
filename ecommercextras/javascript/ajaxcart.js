/* add and remove using ajax... */

;(function($) {
	$(document).ready(function() {
		ajaxCart.addAddLinks();
		ajaxCart.addRemoveLinks();
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

		addRemoveLinks: function () {
			$(".removeLink").click(
				function(){

					var url = $(this).attr("href");
					var el = this;
					$(el).parent("li").css("text-decoration", "line-through");
					$.get(url, function(){ $(el).parent("li").fadeOut();});
					return false;
				}
			);
		},

		loadAjax: function(loadIntoElID, URL, el) {
			$("#" + loadIntoElID).text(ajaxCart.LoadingText);
			$("#" + loadIntoElID).load(URL, {}, function() {$(el).text(ajaxCart.BoughText); ajaxCart.addRemoveLinks();});

			return true;
		}
	}

})(jQuery);