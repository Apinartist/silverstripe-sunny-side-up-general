
var DOBField = {

	ccInputSelector: ".DOCardField input",

	nextFieldSelector: "",

	activeFieldClass: "activeCreditCardItem",

	set_nextFieldSelector: function(v) {
		this.nextFieldSelector = v;
	},

	dobStepThrough: function() {
		jQuery(DOBField.ccInputSelector).each(
			function() {
				jQuery(this).keyup(
					function () {
						var entry = jQuery(this).val();
						var next = false;
						if(4 == entry.length) {
							var next = true;
							var number = 0
							switch(jQuery(this).attr("name")) {
								case "DOBNumber[0]":
									break;
								case "DOBNumber[1]":
									number = 1;
									break;
								case "DOBNumber[2]":
									number = 2;
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
							if(3 == number) {
								jQuery(DOBField.nextFieldSelector).focus();
							}
							else {
								nextName = "DOBNumber["+parseInt(number+1)+"]";
								jQuery("input[name='" + nextName + "']").focus();
								jQuery(DOBField.ccInputSelector).removeClass(DOBField.activeFieldClass);
								jQuery(DOBField.ccInputSelector+"[name='" + nextName + "']").addClass(DOBField.activeFieldClass);
							}
						}
					}
				);
			}
		);
	}
}
JS