jQuery(document).ready(
	function () {
		jQuery(".typography img").css("heigth", "100px");
		jQuery(".typography img").css("width", "200px");

		for(var i = 1; i < 11; i++) {
			var className = ".colour" + i;
			var colourName = jQuery(className).css("background-color");
			jQuery(className).html("<span style=\'color: #fff;\'>" + colourName + "<\/span> <span style=\'color: #000;\'>" + colourName + "<\/span>");
		}
	}
);

