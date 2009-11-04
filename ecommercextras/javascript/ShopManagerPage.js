/**
*@author nicolaas[at]sunnysideup . co . nz
*
**/

(function($){

	$(document).ready(
		function() {
			ShopManagerPage.init();
		}
	);


})(jQuery);


var ShopManagerPage = {

	linkSelectors: "#ShopManagerPageOptionList a",

	inputSelector: "#ShopManagerPageOrderID",

	init: function () {
		jQuery(ShopManagerPage.linkSelectors).click(
			function() {
				var val = parseInt(jQuery(ShopManagerPage.inputSelector).val());
				if(val) {
					jQuery(this).attr("href", jQuery(this).attr("href") + "/" + val);
					return true;
				}
				else {
					alert("please enter an order number first");
					return false;
				}

			}
		)
	}

}


