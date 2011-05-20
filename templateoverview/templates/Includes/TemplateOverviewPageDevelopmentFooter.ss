<% if IncludeTemplateOverviewDevelopmentFooter %><% if ID %>
<div id="TemplateOverviewPageDevelopmentFooter">
	<h3>Templates</h3>
	<p>
		This page uses the <a href="{$TemplateOverviewPage.Link}showmore/$ID" class="IncludeTemplateOverviewDevelopmentFooterClickHere">$ClassName</a> template.
		See <a href="{$TemplateOverviewPage.Link}#sectionFor-$ClassName">complete list</a>.
			<!-- <a href="#" onclick="templateoverviewoverlay.addExtension('{$ClassName}'); " id="overlayFor{$ClassName}" class="overlayLink">add a design overlay.</a> -->
	</p>
	<ul id="TemplateOverviewPageDevelopmentFooterLoadHere"><li class="hiddenListItem">&nbsp;</li></ul>
	<ul id="TemplateOverviewPrevNextList">
		<li>Choose template: <% control TemplateList %><% if Count %><a href="{$FullLink}?flush=1" title="$ClassName - $Title" class="$LinkingMode">$Pos</a><% if Last %><% else %>, <% end_if %><% end_if %><% end_control %></li>
	</ul>
	<% if TemplateOverviewBugs %>
	<h3>Known Bugs</h3>
	<ul id="TemplateOverviewBugs">
	<% control TemplateOverviewBugs %>
		<li><a href="$Link">$Title</a></li>
	<% end_control %>
	</ul>
	<% end_if %>
	<p><a href="$BugManagementLink">Record a bug</a></p>
</div>
<% end_if %><% end_if %>

