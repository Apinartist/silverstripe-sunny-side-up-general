<% if Links %>
<ul class="HeadlineWidgetEntries">
	<% control Links %>
	<li>
		<a class="$LinkOrSection $FirstLast HeadlineWidgetTitle" href="$Link"><span>$MenuTitle</span></a>
		<span class="HeadlineWidgetDate">$Date.Long</span>
	</li>
	<% end_control %>
</ul>
<% else %>
<p>There are no headlines available at present.</p>
<% end_if %>