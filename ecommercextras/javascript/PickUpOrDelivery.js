
(function($){
	$(document).ready(
		function() {
			var options = {
				target:        '#OrderInformationEditable',   // target element(s) to be updated with server response
				beforeSubmit:  PickUpOrDelivery.showRequest,  // pre-submit callback
				success:       PickUpOrDelivery.showResponse  // post-submit callback
			};
			$('#PickUpOrDelivery_Form_ModifierForm').ajaxForm(options);
			$("#PickUpOrDelivery_Form_ModifierForm .Actions").hide(); // does not work!
			$("#PickUpOrDelivery_Form_ModifierForm_PickupOrDeliveryType").change(
				function() {
					$(options.target).css("height", $("#OrderInformationEditable").height() + "px");
					$(options.target).html('<p class="loading">loading updated data ...</p>');
					$("#PickUpOrDelivery_Form_ModifierForm").submit();
				}
			);
		}
	);


	var	PickUpOrDelivery = {
		// pre-submit callback
		showRequest: function (formData, jqForm, options) {
				return true;
		},

		// post-submit callback
		showResponse: function (responseText, statusText)  {
			//redo quantity boxes
			ajaxCart.redoCartAjax();
			//reset height so it can expand...
			$("#OrderInformationEditable").css("height", "auto");
		}


	}
})(jQuery);

