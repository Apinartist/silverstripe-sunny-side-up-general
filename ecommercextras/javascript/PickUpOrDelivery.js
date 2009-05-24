
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
					$("#OrderInformationEditable").css("height", $("#OrderInformationEditable").height() + "px");
					$("#OrderInformationEditable").html('<p class="loading">loading updated data ...</p>');

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
			PickUpOrDelivery.redoCartAjax();
			//reset height so it can expand...
			$("#OrderInformationEditable").css("height", "auto");
		},

		redoCartAjax: function() {
			jQuery('input.ajaxQuantityField').each(
				function() {
					jQuery(this).attr('disabled', false);
					jQuery(this).change(
						function() {
							var name = jQuery(this).attr('name')+ '_SetQuantityLink';
							var setQuantityLink = jQuery('[name=' + name + ']');
							if(jQuery(setQuantityLink).length > 0) {
								setQuantityLink = jQuery(setQuantityLink).get(0);
								if(! this.value) this.value = 0;
								else this.value = this.value.replace(/[^0-9]+/g, '');
								var url = jQuery('base').attr('href') + setQuantityLink.value + '?quantity=' + this.value;
								jQuery.getJSON(url, null, setChanges);
							}
						}
					);
				}
			);
		}

	}
})(jQuery);

