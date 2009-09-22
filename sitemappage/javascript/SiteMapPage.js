
 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 */


;(function($) {
	$(document).ready(
		function() {
			sitemappage.init();
		}
	);

	var sitemappage = {

		outerUlSelector: "ul#SiteMapHolder",

		expanderASelector: "a.siteMapPageExpander",

		init: function() {
			$(sitemappage.outerUlSelector + ' ul').hide();
			$(sitemappage.expanderASelector).click(
				function() {
					var id = $(this).attr('rel');
					if($(this).hasClass("SiteMapNodeExpanded")) {
						$('#list' + id).hide('slow');
						$(this).removeClass("SiteMapNodeExpanded");
						$(this).addClass("SiteMapNodeCompacted");
					}
					else {
						$('#list' + id).show('slow');
						$(this).removeClass("SiteMapNodeCompacted");
						$(this).addClass("SiteMapNodeExpanded");
					}
				}
			);
		}
	}
})(jQuery);