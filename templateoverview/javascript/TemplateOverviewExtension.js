jQuery(document).ready(
	function () {
		jQuery("#TemplateOverviewPageDevelopmentFooter a").click(
			function() {
				var url = jQuery(this).attr("href");
				jQuery("#TemplateOverviewPageDevelopmentFooterLoadHere").html("loading ....");
				jQuery("#TemplateOverviewPageDevelopmentFooterLoadHere").load(url);
				return false;
			}
		);
	}
);

