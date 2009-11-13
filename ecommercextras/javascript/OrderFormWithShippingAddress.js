;(function($) {
	$(document).ready(
		function() {
			OrderFormWithShippingAddress.init();
		}
	);
	var OrderFormWithShippingAddress = {
		init: function(){
			jQuery("#FirstName input, #Surname input").change(
				function() {
					if(
						!jQuery("#ShippingName input").val() ||
						jQuery("#FirstName input").val() == jQuery("#ShippingName input") ||
						jQuery("#Surname input").val() == jQuery("#ShippingName input")
					) {
						jQuery("#ShippingName input").val(jQuery("#FirstName input").val()+" "+jQuery("#Surname input").val());
					}
				}
			);
			jQuery("#ShippingName input").focus(
				function() {
					if(!jQuery("#ShippingName input").val()) {
						jQuery("#ShippingName input").val(jQuery("#FirstName input").val()+" "+jQuery("#Surname input").val());
					}
				}
			);
			jQuery("#City input").change(
				function() {
					if(!jQuery("#ShippingCity input").val() && jQuery("#City input").val()) {
						jQuery("#ShippingCity input").val(jQuery("#City input").val());
					}
				}
			);
			jQuery("#Address input").change(
				function() {
					if(!jQuery("#ShippingAddress input").val() && jQuery("#Address input").val()) {
						jQuery("#ShippingAddress input").val(jQuery("#Address input").val());
					}
				}
			);
			jQuery("#AddressLine2 input").change(
				function() {
					if(!jQuery("#ShippingAddress2 input").val() && jQuery("#AddressLine2 input").val()) {
						jQuery("#ShippingAddress2 input").val(jQuery("#AddressLine2 input").val());
					}
				}
			);
		}
	}
})(jQuery);


