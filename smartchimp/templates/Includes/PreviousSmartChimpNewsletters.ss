<% if SmartChimpNewsletters %>
<div id="PreviousSmartChimpNewsletters">
	<h2>Previous Editions</h2>
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
</div>
<% end_if %>
