var LiveEmailCheckModifier = {

	emailFieldSelector: "input[name='Email']",

	emailFieldError: "Please check your email address.",

	emailFieldCheckingMessage: "checking email address",

	url: "liveemailcheckmodifier/checkemail/?email=",

	alternativeValidationSelector: "#Email span.validation",

	init: function () {
		jQuery(LiveEmailCheckModifier.emailFieldSelector).change(
			function () {
				var email = jQuery(this).val();
				jQuery(this).addClass("loading");
				jQuery(this).val(LiveEmailCheckModifier.emailFieldCheckingMessage);
				var base = jQuery("base").attr("href");
				url = base + LiveEmailCheckModifier.url + email;
				jQuery.get(
					url,
					function(response) {
						jQuery(LiveEmailCheckModifier.alternativeValidationSelector).hide();
						jQuery(LiveEmailCheckModifier.emailFieldSelector).val(email);

						jQuery(LiveEmailCheckModifier.emailFieldSelector).removeClass("loading");
						if(response != "ok") {
							if(response == "invalid") {
								jQuery(LiveEmailCheckModifier.alternativeValidationSelector).show().text(LiveEmailCheckModifier.emailFieldError);
								jQuery(LiveEmailCheckModifier.emailFieldSelector).focus();
							}
							else {
								jQuery(LiveEmailCheckModifier.alternativeValidationSelector).show().text(response);
								if(confirm(response)) {
									window.location = base + "Security/login/?BackURL=" + escape(window.location);
								}
								else {
									jQuery(LiveEmailCheckModifier.emailFieldSelector).focus();
								}
							}
						}
						else {
							jQuery(LiveEmailCheckModifier.alternativeValidationSelector).hide();
						}
					}
				);
			}
		);
	}
}
