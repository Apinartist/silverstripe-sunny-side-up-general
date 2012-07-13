<% if Children %>
<div class="sidebarBox" id="SidebarChildren">
	<h3>In the <i>$MenuTitle</i> section</h3>
	<ul>
		<% control Children %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
	</ul>
</div>
<% end_if %>

<% if Siblings %>
<div class="sidebarBox" id="SidebarSiblings">
	<h3>Related pages</h3>
	<% if Parent %><p class="parentPageInfo">Other pages in the <% control Parent %><a href="$Link">$Title</a><% end_control %> section.</p><% end_if %>
	<ul>
		<% control Siblings %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
	</ul>
</div>
<% end_if %>

<% if SearchForm %>
<div class="sidebarBox" id="SidebarSearch">
	<h3>Search</h3>
	<div class="searchFormOuter">$SearchForm</div>
</div>
<% end_if %>

<% if RandomImage %>
<div class="sidebarBox" id="RandomImage">
	<h3>Random Visual Thought</h3>
	<div id="RandomVisualThought" style="background-image: url($RandomImage(1200).URL); width: 200px; height: 200px;" rel="$RandomImage(1200).URL"></div>
</div>
<% end_if %>
