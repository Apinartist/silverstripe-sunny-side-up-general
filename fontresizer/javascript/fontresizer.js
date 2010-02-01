;
jQuery(document).ready(
	function(){
		fontresizer.init();
	}
);

fontresizer = {

	chooserSelector: ".fontSizeChooser",

	chooserClickersSelector: ".fontSizeChooser a",

	doesNotHaveAlteredFontClass: "doesNotHaveAlteredFont",

	resetSelector: ".resetFont",

	increaseSelector: ".increaseFont",

	decreaseSelector: ".decreaseFont",

	wrapperSelector: "#FontSizeWrapper",

	currentFontSizeInPercentagesSelector: ".currentFontSizeInPercentages",

	size: 100,

	href: "",

	median: 100,

	min: 30, setMin: function(v) {fontresizer.min = parseInt(v);},

	max: 230, setMax: function(v) {fontresizer.max = parseInt(v);},

	init: function () {
		jQuery("." + fontresizer.doesNotHaveAlteredFontClass).hide();
		jQuery(fontresizer.chooserClickersSelector).click(
			function() {

				fontresizer.href = jQuery(this).attr("href");
				jQuery("body").fadeTo(
					"fast",
					0.7,
					function() {
						jQuery.get(
							fontresizer.href,
							function (returnValue) {
								fontresizer.lastSize = fontresizer.size;
								fontresizer.size = returnValue
								if(fontresizer.size == fontresizer.median) {
									jQuery(fontresizer.resetSelector).addClass(fontresizer.doesNotHaveAlteredFontClass).hide();
								}
								else {
									jQuery(fontresizer.resetSelector).removeClass(fontresizer.doesNotHaveAlteredFontClass).show();
								}
								jQuery(fontresizer.increaseSelector+ ", " + fontresizer.decreaseSelector).show();
								if(fontresizer.size >= fontresizer.max) {
									jQuery(fontresizer.increaseSelector).hide();
								}
								else if(fontresizer.size <= fontresizer.min) {
									jQuery(fontresizer.decreaseSelector).hide();
								}
								jQuery(fontresizer.wrapperSelector).css("font-size", fontresizer.size+"%");
								jQuery(fontresizer.currentFontSizeInPercentagesSelector).text(fontresizer.size+"%");
								jQuery("body").fadeTo("fast", 1.3);
							}
						);
					}
				);
				return false;
			}
		)
	}

}

