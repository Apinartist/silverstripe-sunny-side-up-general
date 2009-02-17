<% if thisPageHasShareThis %>
<ul id="shareThisUL">
	<% control Icons %>
	<li class="shareThisLI $Key"><a href="$URL" <% if OnClick %>onclick="$OnClick"<% end_if %> title="$Title"><img src="$ImageSource" alt="$Title" /></a></li>
	<% end_control %>
</ul>
<% end_if %>
