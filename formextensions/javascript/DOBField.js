;(function(jQuery) {
	jQuery(document).ready(
		function() {
			DOBField.dobStepThrough();
			alert("go");
		}
	);
	var DOBField = {

		dobInputSelector: ".dobfield input",

		nextFieldSelector: "",

		activeFieldClass: "activeDOBItem",

		set_nextFieldSelector: function(v) {
			this.nextFieldSelector = v;
		},

		dobStepThrough: function() {
			jQuery(DOBField.dobInputSelector).each(
				function() {
					jQuery(this).keyup(
						function () {
							var entry = jQuery(this).val();
							var next = false;
							if(2 == entry.length) {
								var next = "Month";
								var name = "Day";
								switch(jQuery(this).attr("name")) {
									case "DOB[Day]":
										number = "Month";
										break;
									case "DOB[Month]":
										number = "Month";
										next = "Year";
										break;
									case "DOB[Year]":
										number = "Year";
										break;
									default:
										alert("error");
								}
							}
							else {
								next = false;
							}
							var currentName = "DOBNumber["+number+"]";
							if(next) {
								nextName = "DOB["+next+"]";
								jQuery("input[name='" + nextName + "']").focus();
								jQuery(DOBField.dobInputSelector).removeClass(DOBField.activeFieldClass);
								jQuery(DOBField.dobInputSelector+"[name='" + nextName + "']").addClass(DOBField.activeFieldClass);
							}
						}
					);
				}
			);
		}
	}

})(jQuery);

