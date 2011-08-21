<h1 class="title">$Title</h1>
<div class="leftBit">
	<div id="ContentHolder">$Content</div>
	<% if SmartChimpNewsletters %>
	<div id="PreviousSmartChimpNewsletters">
		<h3>Previous Editions</h3>
		<ul id="SmartChimpNewsletters">
		<% control SmartChimpNewsletters %>
			<li>
				<h5><a href="$PermaLink" class="popup">$Title</a></h5>
				<p class="smartChimpNewslettersDate">$Date.Long</p>
			</li>
		<% end_control %>
		</ul>
	</div>
	<% end_if %>

</div>
<div class="rightBit">
	<% include ContactInfo %>
</div>

