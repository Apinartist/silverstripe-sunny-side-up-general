/**
*@author nicolaas[at]sunnysideup . co . nz
*
**/

(function($){

	$(document).ready(
		function() {
			SalesReport.init();
		}
	);

	var SalesReport = {

		reportID: "",

		formID: "Form_EditForm",

		loadingClass: "loading",


		init: function() {
			var options = {
				beforeSubmit:  SalesReport.showRequest,  // pre-submit callback
				success: SalesReport.showResponse,  // post-submit callback
				url: SalesReportURL
			};
			jQuery('#' + SalesReport.formID).ajaxForm(options);
		},

	// pre-submit callback
		showRequest: function (formData, jqForm, options) {
			for (var i=0; i < formData.length; i++) {
				if ("ID" == formData[i].name) {
					SalesReport.reportID = formData[i].value
				}
			}
			options.url = options.url + SalesReport.reportID;
			return true;
	},

		// post-submit callback
		showResponse: function (responseText, statusText)  {
			if("ok" == responseText) {
				$("li#" +SalesReport.reportID+ " a").click();
			}
			else {
				alert("sorry could not apply filter");
			}
		}
	}

})(jQuery);

