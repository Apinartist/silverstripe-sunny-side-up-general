<div class="sidebarBox" id="SidebarChildren">
	<% if Level(2) %>
		<% if Children %>
	<h3>In this section</h3>
	<ul>
		<% control Children %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
	</ul>
		<% else %><% if Siblings %>
	<h3>In this section</h3>
	<ul>
		<% control Siblings %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
	</ul>
		<% end_if %><% end_if %>

	<% end_if %>

	<div class="clear"></div>
</div>
<div class="sidebarBottom"></div>
