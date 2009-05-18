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

		plusText: "+",

		minusText: "-",

		Level1Class: "L1",

		init: function(mainULid, plusText, minusText) {

			if(mainULid) {this.mainULid = mainULid;}
			if(plusText) {this.plusText = plusText;}
			if(minusText) {this.minusText = minusText;}
			this.collapse(jQuery(this.mainULid));
			jQuery(this.mainULid +" .toggle").click(
				function() {
					jQuery(this).parent().children("ul").children("li").slideToggle();
					if(jQuery(this).hasClass("menuPlus")) {
						jQuery(this).removeClass("menuPlus");
						jQuery(this).addClass("menuMinus");
						jQuery(this).text(jQuery.SilverstripeCollapsableMenu .minusText);
					}
					else {
						jQuery(this).removeClass("menuMinus");
						jQuery(this).addClass("menuPlus");
						jQuery(this).text(jQuery.SilverstripeCollapsableMenu .plusText);
					}
					return false;
				}
			);
		},
		collapse: function(element) {
			jQuery(element).children("li").each(
				function() {
					if(jQuery(this).children("ul").length > 0) {
						if(jQuery(this).hasClass("section") || jQuery(this).hasClass("current")) {
							jQuery(this).prepend('<a href="#" class="toggle menuMinus">'+jQuery.SilverstripeCollapsableMenu .minusText+'</a>');
							if(jQuery(this).hasClass("current")) {
								jQuery(this).children("ul").children("li").each(
									function() {
										if(jQuery(this).children("ul").length > 0) {
											jQuery(this).prepend('<a href="#" class="toggle menuPlus">'+jQuery.SilverstripeCollapsableMenu .plusText+'</a>');
											var element = jQuery(this).children("ul");
											jQuery.SilverstripeCollapsableMenu.collapse(element);
										}
									}
								);
							}
						}
						else {
							jQuery(this).prepend('<a href="#" class="toggle menuPlus">'+jQuery.SilverstripeCollapsableMenu .plusText+'</a>');
							jQuery.SilverstripeCollapsableMenu .collapse(jQuery(this).children("ul"));
						}
					}
					if(!jQuery(this).hasClass(jQuery.SilverstripeCollapsableMenu.Level1Class)) {
						jQuery(this).hide();
					}
				}
			);
		}
	}
});






