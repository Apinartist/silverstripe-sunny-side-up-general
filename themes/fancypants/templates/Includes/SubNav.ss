<% if Menu(2) %>
	<div id="secondarynav">
		<ul>
		<% control Menu(2) %>
		<li class="$LinkingMode"><a href="$Link">$MenuTitle</a>
			<% if LinkingMode != link %>
				<% if Children %>
				<ul class="tertiarynav">
					<% control Children %>
					<li class="$LinkingMode"><a href="$Link">$MenuTitle</a></li>
					<% end_control %>
				</ul>
				<% end_if %>
			<% end_if %>
		</li>
		<% end_control %>
	</ul>
</div>
<% end_if %>
