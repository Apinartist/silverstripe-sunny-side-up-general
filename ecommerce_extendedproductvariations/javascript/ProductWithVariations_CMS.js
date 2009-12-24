
/**
 *@author nicolaas[at]sunnysideup . co . nz
 *@description:
 ** adds a wee bit of functionality to the CMS
 **/



ProductWithVariations_CMS = {

	clicker: function(el) {
		var href = jQuery(el).attr("href");
		var ID = jQuery(el).attr("rel");
		var oldText = jQuery(el).text();
		jQuery(el).text("executing request...");
		jQuery(el).load(
			href,
			function() {
				//hack, but seemed to crash otherwise
				window.location.reload();
			}
		);
		return true;
	}

}

