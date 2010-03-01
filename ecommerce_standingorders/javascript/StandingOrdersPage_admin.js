/**
*@author nicolaas[at]sunnysideup . co . nz
*
**/

(function($){

	$(document).ready(
		function() {
			StandingOrdersPage_admin.init();
		}
	);


})(jQuery);


var StandingOrdersPage_admin = {

	detailClassSelector: ".standingOrderDetails",

	detailIDSelectorPrefix: "#StandingOrderDetails-",

	standingOrderLinkSelector: ".standingOrderLink",

	standingOrderFirstLinkSelector: "#firstStandingOrderLink",

	init: function() {
		jQuery(StandingOrdersPage_admin.detailClassSelector).hide();
		jQuery(StandingOrdersPage_admin.standingOrderLinkSelector).click(
			function(){
				var rel = jQuery(this).attr("rel");
				var id = StandingOrdersPage_admin.detailIDSelectorPrefix+rel;
				var slideUp = 0;
				if(jQuery(id).is(':visible')) {
					var slideUp = 1;
				}
				jQuery(StandingOrdersPage_admin.detailClassSelector).slideUp(
					function() {
						if(!slideUp) {
							jQuery(id).slideDown();
						}
					}
				);
				return false;
			}
		);
		jQuery(StandingOrdersPage_admin.standingOrderFirstLinkSelector).click();
	}


}


