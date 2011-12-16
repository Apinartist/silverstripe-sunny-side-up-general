<div id="Sidebar">
	<div class="sidebarTop"></div>
	<div class="sidebarBox" id="SidebarChildren">
			<% if Children %>
		<h3>In the <i>$MenuTitle</i> section</h3>
		<ul>
			<% control Children %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
		</ul>
			<% end_if %>
	</div>
	<div class="sidebarBox" id="SidebarSiblings">
			<% if Siblings %>
		<h3>Related sections</h3>
		<ul>
			<% control Siblings %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
		</ul>
			<% end_if %>
	</div>
	<div class="sidebarBottom"></div>
</div>
