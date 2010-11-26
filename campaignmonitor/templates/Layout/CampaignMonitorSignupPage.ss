<% if ShowThankYouMessage %>
<div id="CampaignMonitorSignupPageThankYou" class="CampaignMonitorSignupPageDiv">
	$ThankYouMessage
</div>
<% end_if %>

<% if ShowSadToSeeYouGoMessage %>
<div id="CampaignMonitorSignupPageSadToSeeYouGo" class="CampaignMonitorSignupPageDiv">
	$SadToSeeYouGoMessage
</div>
<% end_if %>

<div id="CampaignMonitorSignupPageFormHMTL" class="CampaignMonitorSignupPageDiv">
	$FormHTML
</div>
<% if CampaignMonitorCampaigns %>
<h2>Previous Campaigns</h2>
<ul>
<% control CampaignMonitorCampaigns %><li>$SentDate, $Subject</li><% end_control %>
</ul>
<% end_if %>
