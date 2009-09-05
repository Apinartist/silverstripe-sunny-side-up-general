jQuery(document).ready(
	function () {
		for(var i = 1; i < 6; i++) {
			var className = ".backgroundcolour" + i;
			var colourName = jQuery(className).css("background-color");
			jQuery(className).html("<span style=\'color: #fff;\'>" + colourName + "<\/span> <span style=\'color: #000;\'>" + colourName + "<\/span>");
		}
	}
);

