<ul id="Nav">
<% control Menu(1) %>
<li class="$LinkingMode level1"><span><a class="$LinkingMode level1" href="$Link" title="<% if Children %>Open the $Title.XML section <% else %>Go to the $Title.XML page<% end_if %>">$MenuTitle</a>
	<% if Children %> <% if LinkOrSection = section %>
	<ul class="SubNav">
		<% control Children %>
		<li class="$FirstLast"><a class="$LinkingMode" href="$Link" title="Go to the $Title.XML page">$MenuTitle</a>
			<% if Children %><% if LinkOrSection = section %>
			<ul class="SubSubNav">
				<% control Children %>
				<li class="$FirstLast"><a class="$LinkingMode" href="$Link" title="Go to the $Title.XML page">$MenuTitle</a></li>
				<% end_control %>
			</ul>
			<% end_if %><% end_if %>
		</li>
		<% end_control %>
	</ul>
	<% end_if %> <% end_if %>
</li>
<% end_control %>
</ul>