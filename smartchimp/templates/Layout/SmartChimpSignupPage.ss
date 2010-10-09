		$Content
		$Form
		<h2>Previous Editions</h2>
<% if SmartChimpSignupPage %>
		<ul id="SmartChimpNewsletters">
	<% control SmartChimpSignupPage %>
			<li>
				<h5><a href="$PermaLink" class="popup">$Title</a></h5>
				<p class="smartChimpNewslettersDate">Sent on: $Date.Long</p>
				<p class="smartChimpNewslettersSubject">Subject: $Subject</p>
				<p class="smartChimpNewslettersContent">$TextContent.LimitWordCountXML</p>
			</li>
	<% end_control %>
		</ul>
<% end_if %>







