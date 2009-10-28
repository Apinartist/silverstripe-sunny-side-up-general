
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
		alert("go")
		jQuery(sitemappage.outerUlSelector + ' ul').hide();
		jQuery(sitemappage.expanderASelector).click(
			function() {
				var id = jQuery(this).attr('rel');
				var idSelector = sitemappage.idSelectorPrepend + id;
				if(jQuery(this).hasClass(sitemappage.expandedClass) && jQuery(idSelector).length > 0) {
					jQuery(idSelector).hide(sitemappage.speed);
					jQuery(this).removeClass(sitemappage.expandedClass);
					jQuery(this).addClass(sitemappage.implodedClass);
				}
				else {
					jQuery(idSelector).show(sitemappage.speed);
					jQuery(this).removeClass(sitemappage.implodedClass);
					jQuery(this).addClass(sitemappage.expandedClass);
				}
			}
		);
	}
}