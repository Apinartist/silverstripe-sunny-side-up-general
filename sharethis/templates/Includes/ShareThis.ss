<% if ThisPageHasShareThis %>
<ul id="shareThisUL">
	<% control Icons %><li class="shareThisLI" id="icon-for{$Key}"><a href="$URL" <% if OnClick %>onclick="$OnClick"<% end_if %> title="$Title"><img src="$ImageSource" alt="$Title" /><% if ShowTitle %> $Title<% end_if %></a></li><% end_control %>
	<% if IncludeShareAll %><li>$ShareAll</li><% end_if %>
</ul>
<% end_if %>
