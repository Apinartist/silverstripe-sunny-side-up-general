<ul id="Nav">
	<% control Menu(1) %><li class="$LinkingMode level1"><a class="$LinkingMode level1" href="$Link">$MenuTitle</a>
	<% if Children %> <% if LinkOrSection = section %>
	<ul class="SubNav">
		<% control Children %>
		<li class="$FirstLast $LinkingMode"><a href="$Link">$MenuTitle</a>
			<% if Children %><% if LinkOrSection = section %>
			<ul class="SubSubNav">
				<% control Children %>
				<li class="$FirstLast $LinkingMode"><a href="$Link">$MenuTitle</a></li>
				<% end_control %>
			</ul>
			<% end_if %><% end_if %>
		</li>
		<% end_control %>
	</ul>
	<% end_if %> <% end_if %>
	</li><% end_control %>
</ul>