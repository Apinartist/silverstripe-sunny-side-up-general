
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
})(jQuery);


var sitemappage = {

	outerUlSelector: "ul#SiteMap",

	expanderASelector: "a.siteMapPageExpander",

	expandedClass: "SiteMapNodeExpanded",

	implodedClass: "SiteMapNodeImploded",

	idSelectorPrepend: '#sublist',

	speed: 'slow',

	init: function() {
		jQuery(sitemappage.outerUlSelector + ' ul').hide();
		jQuery(sitemappage.expanderASelector).addClass(sitemappage.implodedClass);
		jQuery(sitemappage.expanderASelector).click(
			function() {
				var id = jQuery(this).attr('rel');
				var idSelector = sitemappage.idSelectorPrepend + id;
				if(jQuery(this).hasClass(sitemappage.implodedClass) && jQuery(idSelector).length > 0) {
					jQuery(idSelector).slideDown(sitemappage.speed);
					jQuery(this).addClass(sitemappage.expandedClass);
					jQuery(this).removeClass(sitemappage.implodedClass);
				}
				else {
					jQuery(idSelector).slideUp(sitemappage.speed);
					jQuery(this).addClass(sitemappage.implodedClass);
					jQuery(this).removeClass(sitemappage.expandedClass);
				}
			}
		);
	}
}