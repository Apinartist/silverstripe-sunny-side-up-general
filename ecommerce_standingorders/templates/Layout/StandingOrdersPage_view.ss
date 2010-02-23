<div id="Account">
	<div class="typography">
	<% if StandingOrder %>
		<% control StandingOrder %>
			<h2>Standing Order #$ID ($Created.Long)</h2>

			<div id="PrintPageIcon">
				<img src="cms/images/pagination/record-print.png" onclick="window.print();">
			</div>

			<div class="clear"><!-- --></div>

			<div class="block">
				<h3>Overview</h3>
				<% if CanModify %>
					<input class="action" type="button" value="Make changes to your standing order" onclick="window.location='$ModifyLink';" />
					<input class="action" type="button" value="Cancel your standing order" onclick="window.location='$CancelLink';" />
				<% end_if %>

				<% include StandingOrder_Content %>

				<% if CanModify %>
					<input class="action" type="button" value="Make changes to your standing order" onclick="window.location='$ModifyLink';" />
					<input class="action" type="button" value="Cancel to your standing order" onclick="window.location='$CancelLink';" />
					<% end_if %>
			</div>

			<div class="clear"><!-- --></div>
		<% end_control %>
	<% else %>
		<p><strong>$Message</strong></p>
	<% end_if %>
	</div>
</div>