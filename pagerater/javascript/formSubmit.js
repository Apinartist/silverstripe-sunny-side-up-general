jQuery(document).ready(
	function() {
		PageRater.init();
		PageRater.relatedFormInit();
	}
);

var PageRater = {

	formSelector: "#Form_PageRatingForm",
	submitButtonSelector: "#Form_PageRatingForm .Actions input",
	loadingMessage: "loading ...",
	formOptions : {
		target: "#Form_PageRatingForm" // target element(s) to be updated with server response
		//dataType:  'xml'        // 'xml', 'script', or 'json' (expected server response type)
	},
	init: function() {

		jQuery(PageRater.submitButtonSelector).click(
			function() {
				jQuery(PageRater.formSelector).ajaxSubmit(PageRater.formOptions);
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
	},

	relatedFormInit: function() {
		alert("start");
		jQuery("form#PageCommentInterface_Form_PostCommentForm").submit(
			function() {
				alert("go");
				return false;
			}
		)
		//$(this).ajaxSubmit(options);
	}

}
