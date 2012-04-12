;(function($) {
	$(document).ready(function() {
		ss_formfield_labels.init();
	});

	var ss_formfield_labels = {

		fieldsSelector: ".typography input, .typography textarea, .typography select",

		hasFocusClass: "hasFocus",

		hasTextClass: "hasText",

		init: function() {

			jQuery(ss_formfield_labels.fieldsSelector).each(
				function() {
					jQuery(this).focus(
						function() {
							var id = jQuery(this).attr("id");
							var labelSelector = "label[for='" + id + "'], #" + id;
							jQuery(labelSelector).addClass(ss_formfield_labels.hasFocusClass);
						}
					);
					jQuery(this).blur(
						function() {
							var id = jQuery(this).attr("id");
							var labelSelector = "label[for='" + id + "'], #" + id;
							jQuery(labelSelector).removeClass(ss_formfield_labels.hasFocusClass);
							if(jQuery(this).val().length == 0) {
								jQuery(labelSelector).removeClass(ss_formfield_labels.hasTextClass);
							}
							else {
								jQuery(labelSelector).addClass(ss_formfield_labels.hasTextClass);
							}
						}
					);
					jQuery(this).keyup(
						function(event) {
							var id = jQuery(this).attr("id");
							var labelSelector = "label[for='" + id + "'], #" + id;
							if(jQuery(this).val().length > 0) {
								jQuery(labelSelector).addClass(ss_formfield_labels.hasTextClass);
							}
							else {
								jQuery(labelSelector).removeClass(ss_formfield_labels.hasTextClass);
							}
						}
					);
				}
			);
		}
	}
})(jQuery);
