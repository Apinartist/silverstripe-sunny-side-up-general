
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

		init: function() {
			jQuery(".webPortfolioMoreInfo").hide();
			jQuery(".webPortfolioShowMore").click(
				function(event) {
					var id = jQuery(this).attr("rel");
					jQuery("#" + id).slideToggle();
					return false;
				}
			)
		}

	}
})(jQuery);
