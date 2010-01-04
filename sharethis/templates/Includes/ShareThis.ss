<% if ThisPageHasShareThis %>
<div id="ShareThisHolder" class="socialNetworkingHolder">
	<% if ShareIcons %>
	<div id="ShareThisHeader" class="socialNetworkingHeader typography"><h5>Share</h5></div>
	<ul id="ShareThisUL" class="socialNetworkingList">
		<% control ShareIcons %><li id="icon-for{$Key}"><a href="$URL" <% if OnClick %>onclick="$OnClick"<% end_if %> title="$Title"><img src="$ImageSource" alt="$Title" width="16" height="16" /><% if ShowTitle %> $Title<% end_if %></a></li><% end_control %>
		<% if IncludeShareAll %><li id="ShareAllLI">$ShareAll</li><% end_if %>
	</ul>
	<% end_if %>
</div>
<% end_if %>
