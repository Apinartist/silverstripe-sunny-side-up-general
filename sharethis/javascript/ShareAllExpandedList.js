
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
				jQuery(this).parent().parent().siblings(".shareAllExpandedListUL").slideToggle();
				return false;
			}

		);
	}




}
