jQuery(document).ready(function() {
	LiveEmailCheckModifier.init();
});

var LiveEmailCheckModifier = {

	emailFieldSelector: "input[name='Email']",

	emailFieldError: "Please check your email address",

	emailFieldRequired: "An email address is required",

	emailFieldCheckingMessage: "checking email address",

	url: "liveemailcheckmodifier/checkemail/?email=",

	alternativeValidationSelector: "#Email span.validation",

	init: function () {
		if(jQuery(LiveEmailCheckModifier.alternativeValidationSelector).length < 1) {
			jQuery("#Email .middleColumn").append('<span class="message validation" style="display: none;"></span>');
		}
		jQuery(LiveEmailCheckModifier.emailFieldSelector).change(
			function () {
				var email = jQuery(this).val();
				if(email) {
					jQuery(LiveEmailCheckModifier.alternativeValidationSelector).text(LiveEmailCheckModifier.emailFieldCheckingMessage).show().addClass("loading");
					var base = jQuery("base").attr("href");
					url = base + LiveEmailCheckModifier.url + email;
					jQuery.get(
						url,
						function(response) {
							jQuery(LiveEmailCheckModifier.alternativeValidationSelector).hide().removeClass("loading");
							jQuery(LiveEmailCheckModifier.emailFieldSelector);
							if(response != "ok") {
								if(response == "invalid") {
									jQuery(LiveEmailCheckModifier.alternativeValidationSelector).show().text(LiveEmailCheckModifier.emailFieldError);
									jQuery(LiveEmailCheckModifier.emailFieldSelector).focus();
								}
								else {
									jQuery(LiveEmailCheckModifier.alternativeValidationSelector).hide();
									if(confirm(response)) {
										window.location = base + "Security/login/?BackURL=" + escape(window.location);
									}
									else {
										jQuery(LiveEmailCheckModifier.emailFieldSelector).focus();
										jQuery(LiveEmailCheckModifier.emailFieldSelector).val("");
									}
								}
							}
							else {
								jQuery(LiveEmailCheckModifier.alternativeValidationSelector).hide();
							}
						}
					);
				}
				else {
					jQuery(LiveEmailCheckModifier.alternativeValidationSelector).text(LiveEmailCheckModifier.emailFieldRequired).show();
				}
			}
		);
	}
}
