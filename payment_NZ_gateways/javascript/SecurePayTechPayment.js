(function($){
	$(document).ready(
		function() {
			SecurePayTechPayment.init();
		}
	);

	var SecurePayTechPayment = {

		hiddenFieldSelector: "#SecurePayTechTestAmountValue, #SecurePayTechCardsToUse, #SecurePayTechCardsExplained",

		allFieldsSelector: "#MethodFields_SecurePayTechPayment input, #MethodFields_SecurePayTechPayment select",

		testAmountSelector: "#SecurePayTechTestAmountValue select",

		testCardSelector: "#SecurePayTechCardsToUse select",

		cardNumberSelector: "#SecurePayTechCreditCardNumber input",

		cardNameSelector: "#SecurePayTechCardHolderName input",

		cardExpirySelector: "#SecurePayTechCardExpiry input",

		shown: 0,

		init: function () {
			jQuery(SecurePayTechPayment.hiddenFieldSelector).hide();
			jQuery(SecurePayTechPayment.allFieldsSelector).focus(
				function () {
					if(!SecurePayTechPayment.shown) {
						jQuery(SecurePayTechPayment.hiddenFieldSelector).show();
						jQuery(SecurePayTechPayment.SecurePayTechTestAmountValue).focus();
						SecurePayTechPayment.shown = 1;
					}
				}
			);
			jQuery(SecurePayTechPayment.testCardSelector).change(
				function () {
					var cardString = jQuery(SecurePayTechPayment.testCardSelector+" option:selected").text();
					alert(cardString);
					if(cardString !== "") {
						var cardArray = cardString.split(",");
						jQuery(SecurePayTechPayment.cardNameSelector).val("Tester Chester");
						jQuery(SecurePayTechPayment.cardNumberSelector).val(new String(cardArray[1]).trim());
						jQuery(SecurePayTechPayment.cardExpirySelector).val(cardArray[2].trim() + cardArray[3].trim());
					}
					else {
						jQuery(SecurePayTechPayment.cardNameSelector).val("");
						jQuery(SecurePayTechPayment.cardNumberSelector).val("");
						jQuery(SecurePayTechPayment.cardExpirySelector).val("");
					}
				}
			);

		}


	}
})(jQuery);

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
