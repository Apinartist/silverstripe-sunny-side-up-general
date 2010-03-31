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

	standingOrderWithoutOutstandingSelector: ".standingOrderWithoutOutstanding",

	showHideWithoutOutstandingSelector: ".showHideWithoutOutstanding",

	showHideAllStandingOrdersSelector: ".showHideAllStandingOrders",

	showHideFutureOrdersSelector: ".showHideFutureOrders",

	futureOrdersSelector: ".TestDraftOrderList li.future, .DraftOrderList li.future",

	init: function() {
		jQuery(StandingOrdersPage_admin.standingOrderWithoutOutstandingSelector).hide();
		jQuery(StandingOrdersPage_admin.detailClassSelector).hide();
		jQuery(StandingOrdersPage_admin.futureOrdersSelector).hide();
		jQuery(StandingOrdersPage_admin.completeStandingOrderSelector).attr("target", StandingOrdersPage_admin.newWindowName);
		jQuery(StandingOrdersPage_admin.completeStandingOrderSelector).click(
			function() {
				var url = jQuery(this).attr("href");
				jQuery(this).removeAttr("href");
				jQuery(this).text("COMPLETED");
				window.open(url, StandingOrdersPage_admin.newWindowName, 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=1024,height=600');
				return false;
			}
		);
		jQuery(StandingOrdersPage_admin.standingOrderLinkSelector).click(
			function(){
				var rel = jQuery(this).attr("rel");
				var id = StandingOrdersPage_admin.detailIDSelectorPrefix + rel;
				jQuery(id).slideToggle();
				return false;
			}
		);
		jQuery(StandingOrdersPage_admin.showHideAllStandingOrdersSelector).click(
			function() {
				var originalText = jQuery(this).text();
				var newText = StandingOrdersPage_admin.replaceHideShow(originalText);
				jQuery(this).text(newText);
				jQuery(StandingOrdersPage_admin.detailClassSelector).slideToggle();
				return false;
			}
		);
		jQuery(StandingOrdersPage_admin.showHideWithoutOutstandingSelector).click(
			function() {
				var originalText = jQuery(this).text();
				var newText = StandingOrdersPage_admin.replaceHideShow(originalText);
				jQuery(this).text(newText);
				jQuery(StandingOrdersPage_admin.standingOrderWithoutOutstandingSelector).slideToggle();
				return false;
			}
		);
		jQuery(StandingOrdersPage_admin.showHideFutureOrdersSelector).click(
			function() {
				var originalText = jQuery(this).text();
				var newText = StandingOrdersPage_admin.replaceHideShow(originalText);
				jQuery(this).text(newText);
				jQuery(StandingOrdersPage_admin.futureOrdersSelector).slideToggle();
				return false;
			}
		);

	},

	replaceHideShow: function(text) {
		var oldText = text;
		var newText = text.replace("hide", "show");
		if(oldText == newText) {
			var newText = text.replace("show", "hide");
		}
		return newText;
	}


}


