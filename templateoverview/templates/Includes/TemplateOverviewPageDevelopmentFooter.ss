<% if IncludeTemplateOverviewDevelopmentFooter %><% if ID %>
<div id="TemplateOverviewPageDevelopmentFooter">
	<p>
		This ages uses <a href="{$TemplateOverviewPage.Link}showmore/$ID" class="IncludeTemplateOverviewDevelopmentFooterClickHere">$ClassName</a> template.
		See <a href="{$TemplateOverviewPage.Link}#sectionFor-$ClassName">complete list</a>.
			<!-- <a href="#" onclick="templateoverviewoverlay.addExtension('{$ClassName}'); " id="overlayFor{$ClassName}" class="overlayLink">add a design overlay.</a> -->
	</p>
	<ul id="TemplateOverviewPageDevelopmentFooterLoadHere"><li class="hiddenListItem">&nbsp;</li></ul>
	<ul id="TemplateOverviewPrevNextList">
	<% control NextTemplateOverviewPage %>
		<% if Count %>
		<li style="background-image: url({$Icon}); " class="TemplateOverviewNext">
			<span class="typo-fullLink"><em>NEXT TEMPLATE:</em> <a href="{$FullLink}?flush=1">$Title (template #$Pos) - $ClassName</a></span>
		</li>
		<% end_if %>
	<% end_control %>
	<% control PrevTemplateOverviewPage %>
		<% if Count %>
		<li style="background-image: url({$Icon}); " class="TemplateOverviewPrevious">
			<span class="typo-fullLink"><em>PREVIOUS TEMPLATE:</em> <a href="{$FullLink}?flush=1">$Title (template #$Pos) - $ClassName</a> </span>
		</li>
		<% end_if %>
	<% end_control %>
		<li>CHOOSE TEMPLATE: <% control TemplateList %><% if Count %><a href="{$FullLink}?flush=1" title="$ClassName - $Title" <% if LinkingMode = current %>style="font-size: 1.5em;"<% end_if %>>$Pos</a><% if Last %><% else %>, <% end_if %><% end_if %><% end_control %></li>
	</ul>

</div>



<% end_if %><% end_if %>

