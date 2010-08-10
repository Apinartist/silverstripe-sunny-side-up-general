
/*
 *@author: nicolaas[at] sunnysideup.co.nz
 *@description: share this expanded list toggle functions
 **/

;(function($) {
	$(document).ready(function() {
		ShareAllExpandedList.init();
	});
})(jQuery);



var ShareAllExpandedList = {

	init: function() {
		jQuery(".shareAllExpandedListUL").css("display", "none");
		jQuery(".shareAllExpandedListHeader a").click(
			function() {
				if(jQuery(".shareAllExpandedListUL").is(":hidden")) {
					jQuery(this).addClass("listShown");
					jQuery(this).parent().parent().siblings(".shareAllExpandedListUL").slideDown("slow");
				}
				else {
					jQuery(this).addClass("listHidden");
					jQuery(this).parent().parent().siblings(".shareAllExpandedListUL").slideUp("fast");
				}
				return false;
			}

		)
		.addClass("listHidden");
	}




}
