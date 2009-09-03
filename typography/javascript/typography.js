jQuery(document).ready(
	function () {
		jQuery(".typography img").css("heigth", "60px");
		jQuery(".typography img").css("width", "100px");

		for(var i = 1; i < 6; i++) {
			var className = ".backgroundcolour" + i;
			var colourName = jQuery(className).css("background-color");
			jQuery(className).html("<span style=\'color: #fff;\'>" + colourName + "<\/span> <span style=\'color: #000;\'>" + colourName + "<\/span>");
		}
	}
);

