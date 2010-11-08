<% if AdvertisementSet %>
<div id="AdvertisementsHolder">
<% control AdvertisementSet %>
	<div class="slide"><% if Link %><a href="$Link"><% end_if %><img src="$Image.FileName" alt="$Title.ATT" /><% if Link %></a><% end_if %></div>
<% end_control %>
</div>
<div id="AdvertisementsPreviousNext"></div>
<% end_if %>
