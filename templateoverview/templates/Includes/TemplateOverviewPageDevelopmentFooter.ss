<% if IncludeTemplateOverviewDevelopmentFooter %><% if ID %>
<div id="TemplateOverviewPageDevelopmentFooter">
	<p>
		This page uses the <a href="{$TemplateOverviewPage.Link}showmore/$ID" class="IncludeTemplateOverviewDevelopmentFooterClickHere">$ClassName</a> template.
		See <a href="{$TemplateOverviewPage.Link}#sectionFor-$ClassName">complete list</a>.
			<!-- <a href="#" onclick="templateoverviewoverlay.addExtension('{$ClassName}'); " id="overlayFor{$ClassName}" class="overlayLink">add a design overlay.</a> -->
	</p>
	<ul id="TemplateOverviewPageDevelopmentFooterLoadHere"><li class="hiddenListItem">&nbsp;</li></ul>
	<ul id="TemplateOverviewPrevNextList">
		<li>CHOOSE TEMPLATE: <% control TemplateList %><% if Count %><a href="{$FullLink}?flush=1" title="$ClassName - $Title" <% if LinkingMode = current %>style="font-size: 1.5em;"<% end_if %>>$Pos</a><% if Last %><% else %>, <% end_if %><% end_if %><% end_control %></li>
	</ul>
	<% if TemplateOverviewBugs %>
	<h3><a href="$BugManagementLink">Known Bugs</a></h3>
	<ul>
	<% control TemplateOverviewBugs %>
		<li>$Title</li>
	<% end_control %>
	</ul>
	<% end_if %>
	<p><a href="$BugManagementLink">Record a bug</a></p>
</div>
<% end_if %><% end_if %>

