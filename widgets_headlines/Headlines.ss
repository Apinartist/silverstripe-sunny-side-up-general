<% if Links %>
<ul class="HeadlineWidgetEntries">
	<% control Links %>
		<li>
			<a class="$LinkOrSection $FirstLast" href="$Link"><span>$MenuTitle</span></a>
			<span>$Date.Long</span>
		</li>
	<% end_control %>
</ul>
<% end_if %>