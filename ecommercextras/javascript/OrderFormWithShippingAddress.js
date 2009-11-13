;(function($) {
	$(document).ready(
		function() {
			OrderFormWithShippingAddress.init();
		}
	);
	var OrderFormWithShippingAddress = {
		init: function(){

			// invoice field
			var FirstName =  jQuery("#FirstName input").val();
			var Surname =  jQuery("#FirstName input").val();
			var Address =  jQuery("#Address input").val();
			var AddressLine2 =  jQuery("#AddressLine2 input").val();
			var City =  jQuery("#City input").val();

			//shipping fields
			var ShippingName =  jQuery("#ShippingName input").val();
			var ShippingCity =  jQuery("#ShippingCity input").val();
			var ShippingAddress =  jQuery("#ShippingAddress input").val();
			var ShippingAddress2 =  jQuery("#ShippingAddress2 input").val();

			//name
			jQuery("#FirstName input, #Surname input").change(
				function() {
					if(
						!ShippingName ||
						FirstName == ShippingName ||
						Surname == ShippingName
					) {
						jQuery("#ShippingName input").val(FirstName+" "+Surname);
					}
				}
			);
			jQuery("#ShippingName input").focus(
				function() {
					if(!ShippingName && (FirstName || Surname)) {
						jQuery("#ShippingName input").val(FirstName+" "+Surname);
					}
				}
			);

			//address
			jQuery("#Address input").change(
				function() {
					if(!ShippingAddress && Address) {
						jQuery("#ShippingAddress input").val(Address);
					}
				}
			);
			jQuery("#AddressLine2 input").change(
				function() {
					if(!ShippingAddress2 && AddressLine2) {
						jQuery("#ShippingAddress2 input").val(AddressLine2);
					}
				}
			);

			//city
			jQuery("#City input").change(
				function() {
					if(!ShippingCity && City) {
						jQuery("#ShippingCity input").val(City);
					}
				}
			);
		}
	}
})(jQuery);


