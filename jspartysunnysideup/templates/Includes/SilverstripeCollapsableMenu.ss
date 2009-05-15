<% control Menu(1) %><% if ShowInMenus %>
	<li class="L1 $CurrentOrSection $URLSegment $FirstLast" ><a href="$Link" title="Go to the $MenuTitle.XML page">$MenuTitle</a>
		<% if Children %>
		<ul>
			<% control Children %><% if ShowInMenus %>
			<li class="L2 $CurrentOrSection" ><a href="$Link" title="Go to the $MenuTitle.XML page">$MenuTitle</a>
				<% if Children %>
				<ul>
					<% control Children %><% if ShowInMenus %>
					<li class="L3 $CurrentOrSection"><a href="$Link" title="Go to the $MenuTitle.XML page">$MenuTitle</a></li>
					<% end_if %><% end_control %>
				</ul><% end_if %>
			</li><% end_if %><% end_control %>
		</ul><% end_if %>
	</li>
<% end_if %>
<% end_control %>