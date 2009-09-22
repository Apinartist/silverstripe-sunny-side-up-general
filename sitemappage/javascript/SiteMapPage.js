
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

		expandedClass: "SiteMapNodeExpanded",

		implodedClass: "SiteMapNodeImploded",

		idSelectorPrepend: '#sublist',

		init: function() {
			$(sitemappage.outerUlSelector + ' ul').hide();
			$(sitemappage.expanderASelector).click(
				function() {
					var id = $(this).attr('rel');
					var idSelector ==  sitemappage.idSelectorPrepend + id;
					if($(this).hasClass(sitemappage.expandedClass) && $(idSelector).length > 0) {
						$(idSelector).hide('slow');
						$(this).removeClass(sitemappage.expandedClass);
						$(this).addClass(sitemappage.implodedClass);
					}
					else {
						$(idSelector).show('slow');
						$(this).removeClass(sitemappage.implodedClass);
						$(this).addClass(sitemappage.expandedClass);
					}
				}
			);
		}
	}
})(jQuery);