/*
 *@author nicolaas[at]sunnysideup.co.nz
 *
 **/

;(function($) {
	$(document).ready(function() {
		initFunctions.imageFixes();
		initFunctions.setupRemoveDefault();
		initFunctions.externalLinks();
		initFunctions.removeTypographyWithinTypography();
	});

	var initFunctions = {
		imageFixes: function() {
			$(".typography img[align='left']").css("float", "left");
			$(".typography img[align='right']").css("float", "right");
			$("img").removeAttr("title", "").removeAttr("align");
		},

		setupRemoveDefault: function () {
			$("input.nolabel").bind("click focus",
				function () {
			 		if(!$(this).attr("rel") || $(this).attr("rel") == $(this).val()) {
						if(!$(this).attr("rel")) {
				 			$(this).attr("rel", $(this).val());
						}
						$(this).val("");
						$(this).blur(
							function() {
								if(!$(this).val()) {
					 				$(this).val($(this).attr("rel"));
								}
				 			}
						);
			 		}
				}
		 	);
		},

		externalLinks: function () {
			$('a[href^="http://"], a.externalLink').each(
				function(i){
					var link = "" + $(this).attr("href");
					var currentSite = "" + window.location;
					var cutOff = 0 + currentSite.indexOf( "/", 10 );
					if(link.substring(0, cutOff) != currentSite.substring(0,cutOff)) {
						this.target= "_blank";
						$(this).addClass("externalLink");
					}
				}
			);
		},

		removeTypographyWithinTypography: function() {
			jQuery(".typography .typography").each(
				function() {
					jQuery(this).removeClass("typography");
				}
			);
		}
	}
})(jQuery);
