/**
* Note: can not do ajax query, because it changes the recommended products
* reload page if products have been added
*
*
**/

(function($){

	$(document).ready(
		function() {
			SearchableOrderReport.init();
		}
	);

	var SearchableOrderReport = {


		formID: "Form_EditForm",

		loadingClass: "loading",


		init: function() {
			var options = {
				beforeSubmit:  SearchableOrderReport.showRequest,  // pre-submit callback
				success: SearchableOrderReport.showResponse,  // post-submit callback
				url: SearchableOrderReportURL
			};
			jQuery('#' + SearchableOrderReport.formID).ajaxForm(options);
		},

	// pre-submit callback
		showRequest: function (formData, jqForm, options) {
			// formData is an array; here we use $.param to convert it to a string to display it
			// but the form plugin does this for you automatically when it submits the data
			var queryString = jQuery.param(formData);

			// jqForm is a jQuery object encapsulating the form element.  To access the
			// DOM element for the form do this:
			// var formElement = jqForm[0];

			alert('About to submit: \n\n' + queryString);

			// here we could return false to prevent the form from being submitted;
			// returning anything other than false will allow the form submit to continue
			return true;
	},

		// post-submit callback
		showResponse: function (responseText, statusText)  {
			if("ok" == responseText) {
				$("li#SearchableOrderReport a").click();
			}
			else {
				alert("sorry could not apply filter");
			}
		}
	}

})(jQuery);

