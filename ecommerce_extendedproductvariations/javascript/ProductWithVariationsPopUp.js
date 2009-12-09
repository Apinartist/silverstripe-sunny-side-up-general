
/**
 *@author nicolaas[at]sunnysideup . co . nz
 *@description:
 * pop-up for ModelAdmin CMS
 **/

(function($){
	$(document).ready(
		function() {
			$(".ProductWithVariationsPopUp").click(
				function() {
					ProductWithVariationsPopUp.popUp($(this).href);
				}
			);
		}
	);


})(jQuery);


ProductWithVariationsPopUp = {

	popUp: function (URL) {
		day = new Date();
		id = day.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=400,left = 440,top = 200');");
	}
}

