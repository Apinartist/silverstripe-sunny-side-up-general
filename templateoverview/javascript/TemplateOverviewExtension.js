jQuery(document).ready(
	function () {
		templateoverviewextension.init();
	}
);

var templateoverviewextension = {

	prettyphotoJSlocation: "prettyphoto/javascript/jquery.prettyPhoto.js",

	prettyphotoCSSlocation: "prettyphoto/css/prettyPhoto.css",

	init: function() {
		jQuery(".IncludeTemplateOverviewDevelopmentFooterClickHere").click(
			function() {
				templateoverviewextension.javascriptImporter(templateoverviewextension.prettyphotoJSlocation);
				templateoverviewextension.cssImporter(templateoverviewextension.prettyphotoCSSlocation);
				var url = jQuery(this).attr("href");
				jQuery("#TemplateOverviewPageDevelopmentFooterLoadHere").html("<li>loading ....</li>");
				jQuery("#TemplateOverviewPageDevelopmentFooterLoadHere").load(url);
				return false;
			}
		);
	},

	javascriptImporter: function (src){
		var fileref = document.createElement('script');
		fileref.setAttribute('src',src);
		fileref.setAttribute('type','text/javascript');
		document.getElementsByTagName('head')[0].appendChild(fileref);
	},

	cssImporter: function (src){
		var fileref=document.createElement("link");
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("type", "text/css");
		fileref.setAttribute("href", src);
		document.getElementsByTagName("head")[0].appendChild(fileref);
	}

}