jQuery(document).ready(
	function() {
		PageRater.init();
	}
);

var PageRater = {

	formSelector: "#Form_PageRatingForm",
	submitButtonSelector: "#Form_PageRatingForm .Actions input",
	loadingMessage: "loading ...",

	init: function() {
		var options = {
			target: "#Form_PageRatingForm" // target element(s) to be updated with server response
			//dataType:  'xml'        // 'xml', 'script', or 'json' (expected server response type)
		};
		jQuery(PageRater.submitButtonSelector).click(
			function() {
				jQuery(PageRater.formSelector).ajaxSubmit(options);
				return false;
			}
		)
	},


	showRequest: function (formData, jqForm, options) {
		jQuery(PageRater.formSelector).html(PageRater.loadingMessage);
		return true;
	},

	showResponse: function (responseText, statusText)  {
		alert(responseText);
		jQuery(PageRater.formSelector).html(responseText);
	}

}
