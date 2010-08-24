<% if Advertisements %>
<div id="AdvertisementsHolder">
<% control Advertisements %>
	<div class="slide"><% if Link %><a href="$Link.URL"><% end_if %><img src="$Image.FileName" alt="$Title.ATT" /><% if Link %></a><% end_if %></div>
<% end_control %>
</div>
<div id="AdvertisementsPreviousNext"></div>
<% end_if %>
