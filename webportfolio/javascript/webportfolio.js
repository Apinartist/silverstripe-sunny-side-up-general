
 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 *
 *
 *
 *
 */


;(function($) {
	$(document).ready(
		function() {
			webportfolio.init();
		}
	);

	var webportfolio = {

		showMoreSelector: "a.webPortfolioShowMore",

		moreInfoSelector: "div.webPortfolioMoreInfo",

		init: function() {
			$(webportfolio.moreInfoSelector).hide();
			$(webportfolio.showMoreSelector).click(
				$(this).next(webportfolio.moreInfoSelector).toggleSlide();
			);
		}

	}
})(jQuery);
