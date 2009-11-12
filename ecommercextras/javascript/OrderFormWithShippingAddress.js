;(function($) {
	$(document).ready(
		function() {
			OrderFormWithShippingAddress.init();
		}
	);
	var OrderFormWithShippingAddress = {
		init: function {
			jQuery("#FirstName input, #Surname input").change(
				function() {
					if(!jQuery("#ShippingName input").val()) {
						jQuery("#ShippingName input").val(jQuery("#FirstName input").val()+" "+jQuery("#Surname input").val());
					}
				}
			);
			jQuery("#City inpu").change(
				function() {
					if(!jQuery("#ShippingCity input").val()) {
						jQuery("#ShippingCity input").val(val = jQuery("#City").val());
					}
				}
			);
		}
	}
})(jQuery);


