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

		dropdownStatusArray: new Array(),

		init: function() {
			SalesReport.initiatePopUps();
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
				$("a#tab-Root_Report").click();
			}
			else {
				alert("sorry could not apply filter");
			}
		},

		initiatePopUps: function() {
			$(".makeIntoPopUp").livequery('click',
				function(event) {
					day = new Date();
					id = day.getTime();
					url = $(this).attr("href");
					window.open(url, id, 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=1,width=770,height=800,left = 0,top = 0');
					return false;
				}
			);
		},
		initiateStatusUpdates() {
			var $("TEMPStatus select").html();
			$(".statusDropdownChange").each(
				function() {


				}
			);



		}
	}

})(jQuery);




