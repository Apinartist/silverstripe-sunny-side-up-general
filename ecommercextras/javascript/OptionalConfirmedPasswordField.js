/* add and remove using ajax... */

;(function($) {
	$(document).ready(
		function() {
			OptionalConfirmedPasswordField.init();
		}
	);
})(jQuery);

var OptionalConfirmedPasswordField = {

	visible: "1",

	init: function() {
		jQuery(".showOnClick a").click(
			function() {
				jQuery(this).next(".showOnClickContainer").slideToggle();
				if(OptionalConfirmedPasswordField.visible) {
					OptionalConfirmedPasswordField.visible = "";
					}
				else {
					OptionalConfirmedPasswordField.visible = "1";
					jQuery(this).hide();
				}
				jQuery(this).next(".showOnClickContainer").children("input[type='hidden']").val(OptionalConfirmedPasswordField.visible);
				return false;
			}

		);
		jQuery(".showOnClick a").click();
	}

}


