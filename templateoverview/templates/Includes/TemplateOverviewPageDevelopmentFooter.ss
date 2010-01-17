<% if IncludeTemplateOverviewDevelopmentFooter %><% if ID %>
<div id="TemplateOverviewPageDevelopmentFooter">
	<p>
		The template for this page is called <a href="{$TemplateOverviewPage.Link}#sectionFor-$ClassName">$ClassName</a>.
		Please make sure to
			<a href="{$TemplateOverviewPage.Link}showmore/$ID" class="IncludeTemplateOverviewDevelopmentFooterClickHere">compare to design and check further instructions</a>.
			<br />
			<a href="#" onclick="templateoverviewoverlay.addExtension('{$ClassName}'); " id="overlayFor{$ClassName}" class="overlayLink">add a design overlay</a>.
	</p>
	<ul id="TemplateOverviewPageDevelopmentFooterLoadHere"><li class="hiddenListItem">&nbsp;</li></ul>
</div>
<% end_if %><% end_if %>