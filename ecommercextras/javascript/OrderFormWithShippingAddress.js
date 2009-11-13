;(function($) {
	$(document).ready(
		function() {
			OrderFormWithShippingAddress.init();
		}
	);
	var OrderFormWithShippingAddress = {
		init: function(){

			//name
			jQuery("#FirstName input, #Surname input").change(
				function() {
					var FirstName =  jQuery("#FirstName input").val();
					var Surname =  jQuery("#Surname input").val();
					var ShippingName =  jQuery("#ShippingName input").val();
					if(!ShippingName || (FirstName == ShippingName && Surname) || (Surname == ShippingName && FirstName)) {
						jQuery("#ShippingName input").val(FirstName+" "+Surname);
					}
				}
			);
			jQuery("#ShippingName input").focus(
				function() {
					var FirstName =  jQuery("#FirstName input").val();
					var Surname =  jQuery("#Surname input").val();
					var ShippingName =  jQuery("#ShippingName input").val();
					if(!ShippingName && (FirstName || Surname)) {
						jQuery("#ShippingName input").val(FirstName+" "+Surname);
					}
				}
			);

			//address
			jQuery("#Address input").change(
				function() {
					var Address =  jQuery("#Address input").val();
					var ShippingAddress =  jQuery("#ShippingAddress input").val();
					if(!ShippingAddress && Address) {
						jQuery("#ShippingAddress input").val(Address);
					}
				}
			);
			jQuery("#AddressLine2 input").change(
				function() {
					var AddressLine2 =  jQuery("#AddressLine2 input").val();
					var ShippingAddress2 =  jQuery("#ShippingAddress2 input").val();
					if(!ShippingAddress2 && AddressLine2) {
						jQuery("#ShippingAddress2 input").val(AddressLine2);
					}
				}
			);

			//city
			jQuery("#City input").change(
				function() {
					var City =  jQuery("#City input").val();
					var ShippingCity =  jQuery("#ShippingCity input").val();
					if(!ShippingCity && City) {
						jQuery("#ShippingCity input").val(City);
					}
				}
			);
		}
	}
})(jQuery);


