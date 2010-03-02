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

	completeStandingOrderSelector: ".completeStandingOrder",

	newWindowName: ".completeStandingOrder",

	init: function() {
		jQuery(StandingOrdersPage_admin.detailClassSelector).hide();
		jQuery(StandingOrdersPage_admin.completeStandingOrderSelector).attr("target", StandingOrdersPage_admin.newWindowName);
		jQuery(StandingOrdersPage_admin.completeStandingOrderSelector).click(
			function() {
				var url = jQuery(this).attr("href");
				jQuery(this).removeAttr("href");
				jQuery(this).text("COMPLETED");
				window.open(url, StandingOrdersPage_admin.newWindowName, 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=1024,height=600');
				return false;
			}


		)
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


