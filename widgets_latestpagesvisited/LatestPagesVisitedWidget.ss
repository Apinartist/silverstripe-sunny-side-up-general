<ul class="LatestPagesVisitedWidget">
<% if LatestPages %>
	<% control LatestPages %>
		<li><a class="$LinkOrSection" href="$URLSegment" title="$Title"><span>$MenuTitle</span></a></li>
	<% end_control %>
<% else %>
	<li>start browsing to see entries here</li>
<% end_if %>
</ul>