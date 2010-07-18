s<% if IncludeTemplateOverviewDevelopmentFooter %><% if ID %>
<div id="TemplateOverviewPageDevelopmentFooter">
	<p>
		The template for this page is called <a href="{$TemplateOverviewPage.Link}#sectionFor-$ClassName">$ClassName</a>.
		Please make sure to
			<a href="{$TemplateOverviewPage.Link}showmore/$ID" class="IncludeTemplateOverviewDevelopmentFooterClickHere">compare to design and check further instructions</a>.
			<br />
			<a href="#" onclick="templateoverviewoverlay.addExtension('{$ClassName}'); " id="overlayFor{$ClassName}" class="overlayLink">add a design overlay.</a>
	</p>
	<ul id="TemplateOverviewPageDevelopmentFooterLoadHere"><li class="hiddenListItem">&nbsp;</li></ul>
	<ul id="TemplateOverviewPrevNextList">
	<% control NextTemplateOverviewPage %>
		<% if Count %>
		<li style="background-image: url({$Icon}); " class="TemplateOverviewNext">
			<span class="typo-fullLink"><em>NEXT PAGE TO CHECK:</em> <a href="{$FullLink}?flush=1">$Title (template #$Pos) - $ClassName</a></span>
		</li>
		<% end_if %>
	<% end_control %>
	<% control PrevTemplateOverviewPage %>
		<% if Count %>
		<li style="background-image: url({$Icon}); " class="TemplateOverviewPrevious">
			<span class="typo-fullLink"><em>PREVIOUS PAGE TO CHECK:</em> <a href="{$FullLink}?flush=1">$Title (template #$Pos) - $ClassName</a> </span>
		</li>
		<% end_if %>
	<% end_control %>
		<li>CHOOSE TEMPLATE: <% control TemplateList %><% if Count %><a href="{$FullLink}?flush=1" title="$ClassName.ATT - $Title.ATT" <% if LinkingMode = current %>style="font-size: 1.5em;"<% end_if %>>$Pos</a><% if Last %><% else %>, <% end_if %><% end_if %><% end_control %></li>
	</ul>

</div>



<% end_if %><% end_if %>

