jQuery(document).ready(
	function() {
		PageRater.init();
	}
);

var PageRater = {

	init: function() {
		var options = {
			target:        '#PageRating'   // target element(s) to be updated with server response
			//dataType:  'xml'        // 'xml', 'script', or 'json' (expected server response type)
		};
		jQuery("#Form_PageRatingForm_action_doPageRating").click(
			function() {
				jQuery("#Form_PageRatingForm").ajaxSubmit(options);
				return false;
			}
		);
		jQuery(".optionset input").click(
			function() {
				jQuery("#Form_PageRatingForm_action_doPageRating").click();
				return false;
			}
		);
	}


	showRequest: function (formData, jqForm, options) {
		jQuery("#PageRating").html("loading ...");
		return true;
	}

	showResponse: function (responseText, statusText)  {
		alert(responseText);
		jQuery("#PageRating").html(responseText);
	}

}
