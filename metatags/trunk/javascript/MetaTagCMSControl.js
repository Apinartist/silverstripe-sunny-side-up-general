// prepare the form when the DOM is ready

jQuery(document).ready(
	function() {
		MetaTagCMSControl.init();
	}
);
var MetaTagCMSControl = {

	fieldName: '',

	delegateRootSelector: "#MetatagOuterHolder",

	init: function(){
		var options = {
			target:             '.response',   // target element(s) to be updated with server response
			beforeSubmit:       MetaTagCMSControl.showRequest,  // pre-submit callback
			success:            MetaTagCMSControl.showResponse,  // post-submit callback
			beforeSerialize:    MetaTagCMSControl.fixSerialize
			// other available options:
			//url:       url         // override for form's 'action' attribute
			//type:      type        // 'get' or 'post', override for form's 'method' attribute
			//dataType:  null        // 'xml', 'script', or 'json' (expected server response type)
			//clearForm: true        // clear all form fields after successful submit
			//resetForm: true        // reset the form after successful submit

			// jQuery.ajax options can be used here too, for example:
			//timeout:   3000
		};

		// bind form using 'ajaxForm'
		jQuery('#MetaTagCMSControlForm').ajaxForm(options);
		//submit on change
		jQuery(MetaTagCMSControl.delegateRootSelector).delegate(
			'input, textarea',
			"change",
			function() {
				jQuery(this).parent().removeClass("lowRes").addClass("highRes");
				MetaTagCMSControl.fieldName = jQuery(this).attr("id");
				jQuery('#MetaTagCMSControlForm').submit();
			}
		);
		jQuery("#MetatagOuterHolder .newWindow").attr("target", "_blank");
		jQuery("#MetatagOuterHolder .actions ul, #MetatagOuterHolder  tr.subsequentActions").hide();
		jQuery(MetaTagCMSControl.delegateRootSelector).delegate(
			".bactchactions a",
			"click",
			function(event) {
				jQuery(".actions ul, tr.subsequentActions").slideToggle();
				event.preventDefault();
				return false;
			}
		);
		jQuery("a.ajaxify").click(
			function(event) {
				event.preventDefault();
				jQuery('tbody').fadeTo("fast", "0.5");
				var url = jQuery(this).attr("href");
				jQuery.get(
					url,
					function(data) {
						jQuery('tbody').html(data);
						jQuery('.response').text("records updated ....");
						jQuery('tbody').fadeTo(
							"fast",
							"1",
							function() {MetaTagCMSControl.init();}
						);
					},
					"html"
				);

			}
		)
	},




	// pre-submit callback
	showRequest: function(formData, jqForm, options) {
		// formData is an array; here we use jQuery.param to convert it to a string to display it
		// but the form plugin does this for you automatically when it submits the data
		var queryString = jQuery.param(formData);

		// jqForm is a jQuery object encapsulating the form element.  To access the
		// DOM element for the form do shiothis:
		// var formElement = jqForm[0];
		//alert('About to submit: \n\n' + queryString);
		return true;
	},

	// post-submit callback
	showResponse: function (responseText, statusText, xhr, jQueryform)  {
		// for normal html responses, the first argument to the success callback
		// is the XMLHttpRequest object's responseText property

		// if the ajaxForm method was passed an Options Object with the dataType
		// property set to 'xml' then the first argument to the success callback
		// is the XMLHttpRequest object's responseXML property

		// if the ajaxForm method was passed an Options Object with the dataType
		// property set to 'json' then the first argument to the success callback
		// is the json data object returned by the server

		//alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + '\n\nThe output div should have already been updated with the responseText.');
	},

	fixSerialize: function ($form, options) {
		//alert(MetaTagCMSControl.fieldName);
		jQuery("#FieldName").attr("value",MetaTagCMSControl.fieldName);
	}
}
