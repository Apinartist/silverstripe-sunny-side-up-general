<% if ThisPageHasShareThis %>
<div class="ShareThisHolder socialNetworkingHolder">
	<% if ShareIcons %>
	<div class="ShareThisHeader socialNetworkingHeader typography"><h5>Share</h5></div>
	<ul class="ShareThisUL socialNetworkingList">
		<% control ShareIcons %><li class="icon-for{$Key}"><a href="$URL" <% if OnClick %>onclick="$OnClick"<% end_if %> title="$Title"><img src="$ImageSource" alt="$Title"<% if UseStandardImage %> width="16" height="16"<% end_if %> /><% if ShowTitle %> $Title<% end_if %></a></li><% end_control %>
		<% if IncludeShareAll %><li class="ShareAllLI"><% include ShallAllDropdown %></li><% end_if %>
	</ul>
	<% end_if %>
</div>
<% end_if %>
