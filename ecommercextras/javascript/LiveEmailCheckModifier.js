;(function($) {
	$(document).ready(
		function() {
			LiveEmailCheckModifier.init();
		}
	);
})(jQuery);

var LiveEmailCheckModifier = {

	emailFieldSelector: "input[name='Email']",

	emailFieldError: "Please check your email address.",

	emailFieldCheckingMessage: "checking email address",

	init: function () {
		jQuery(AjaxCheckout.emailFieldSelector).change(
			function () {
				var email = jQuery(this).val();
				jQuery(this).addClass("loading");
				jQuery(this).val(AjaxCheckout.emailFieldCheckingMessage);
				var base = jQuery("base").attr("href");
				url = base + "liveemailcheckmodifier/checkemail/?email=" + email;
				jQuery.get(
					url,
					function(response) {
						jQuery(AjaxCheckout.emailFieldSelector).val(email);
						jQuery(AjaxCheckout.emailFieldSelector).removeClass("loading");
						if(response != "ok") {
							if(response == "invalid") {
								alert(AjaxCheckout.emailFieldError);
								jQuery(AjaxCheckout.emailFieldSelector).focus();
							}
							else {
								if(confirm(response)) {
									window.location = base + "Security/login/?BackURL=" + escape(window.location);
								}
								else {
									jQuery(AjaxCheckout.emailFieldSelector).focus();
								}
							}
						}
					}
				);
			}
		);
	}
}
