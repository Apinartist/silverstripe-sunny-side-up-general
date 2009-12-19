<% if ThisPageHasSocialNetworkingLinks %>
<ul id="shareThisUL">
	<% control SocialNetworkingLinksDataObjects %><li class="socialNetworkingLinkLI"><a href="$URL.URL">$Icon.SetHeight(32)<% if ShowTitle %> $Title<% end_if %></a></li><% end_control %>
</ul>
<% end_if %>
