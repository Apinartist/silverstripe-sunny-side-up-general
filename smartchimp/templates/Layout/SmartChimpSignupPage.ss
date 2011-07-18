		$Content
		$Form
		<h2>Previous Editions</h2>
<% if SmartChimpNewsletters %>
		<ul id="SmartChimpNewsletters">
	<% control SmartChimpNewsletters %>
			<li>
				<h5><a href="$PermaLink" class="popup">$Title</a></h5>
				<p class="smartChimpNewslettersDate">Sent on: $Date.Long</p>
				<p class="smartChimpNewslettersSubject">Subject: $Subject</p>
				<p class="smartChimpNewslettersContent">$TextContent.LimitWordCountXML</p>
			</li>
	<% end_control %>
		</ul>
<% end_if %>







