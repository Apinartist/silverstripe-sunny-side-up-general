/*
 WRITTEN BY nicolaas [at] sunnysideup.co.nz

 This sets up a collapsable menu (a la the left-hand in the CMS) for a typical SS menu structure.
 Leaving the "current" and "section" marked nodes open by default.

 required template:

<% control Menu(1) %><% if ShowInMenus %>
	<li class="L1 jQueryLinkingMode" ><a href="jQueryLink" title="Go to the jQueryMenuTitle.XML page">jQueryMenuTitle</a>
		<% if Children %>
		<ul>
			<% control Children %><% if ShowInMenus %>
			<li class="L2 jQueryLinkingMode" ><a href="jQueryLink" title="Go to the jQueryMenuTitle.XML page">jQueryMenuTitle</a>
				<% if Children %>
				<ul>
					<% control Children %><% if ShowInMenus %>
					<li class="L3 jQueryLinkingMode"><a href="jQueryLink" title="Go to the jQueryMenuTitle.XML page">jQueryMenuTitle</a></li>
					<% end_if %><% end_control %>
				</ul><% end_if %>
			</li><% end_if %><% end_control %>
		</ul><% end_if %>
	</li>
<% end_if %>
<% end_control %>

NOTA BENE: I tried doing a recursive template (include a template within itself (i.e. if children include myself again)) but that crashed!

initiate like this:

	jQuery(document).ready(function() {
		SilverstripeCollapsableMenu .init("myMenuOuterULidHere", "+", "-");
	});

*/


jQuery.extend({

	SilverstripeCollapsableMenu  : {

		mainULid: "#menu",

		allLinkPrepend: "[go to: ",

		allLinkAppend: "]",

		Level1Class: "L1",

		init: function(mainULid, allLinkPrepend, allLinkAppend) {

			if(mainULid) {this.mainULid = mainULid;}
			if(allLinkPrepend) {this.allLinkPrepend = allLinkPrepend;}
			if(allLinkAppend) {this.allLinkAppend = allLinkAppend;}
			this.collapse(jQuery(this.mainULid));
			jQuery(this.mainULid +" li.toggle").click(
				function() {
					jQuery(this).children("ul").slideToggle();
					if(jQuery(this).hasClass("menuPlus")) {
						jQuery(this).removeClass("menuPlus");
						jQuery(this).addClass("menuMinus");
					}
					else {
						jQuery(this).removeClass("menuMinus");
						jQuery(this).addClass("menuPlus");
					}
					return false;
				}
			);
		},

		collapse: function(element) {
			jQuery(element).children("li").each(
				function() {
					if(jQuery(this).children("ul").length > 0) {
						jQuery(this).addClass("toggle");
						var name = jQuery(this).children("a").text();
						var link = jQuery(this).children("a").attr("href");
						jQuery(this).children("ul").prepend('<li><a href="' + link + '" class="SCMAllLink">'+jQuery.SilverstripeCollapsableMenu.allLinkPrepend + name + jQuery.SilverstripeCollapsableMenu.allLinkAppend+'</a></li>');
						if(jQuery(this).hasClass("section") || jQuery(this).hasClass("current")) {
							if(jQuery(this).hasClass("current")) {
								jQuery(this).children("ul").children("li").each(
									function() {
										if(jQuery(this).children("ul").length > 0) {
											jQuery.SilverstripeCollapsableMenu.collapse(element);
										}
									}
								);
							}
						}
						else {
							jQuery.SilverstripeCollapsableMenu.collapse(jQuery(this).children("ul"));
						}
					}
					if(!jQuery(this).hasClass(jQuery.SilverstripeCollapsableMenu.Level1Class)) {
						jQuery(this).parent("ul").hide();
					}
				}
			);
		}



	}
});






