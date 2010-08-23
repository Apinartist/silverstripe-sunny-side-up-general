<% if Advertisements %>
<div id="AdvertisementsHolder">
<% control Advertisements %>
	<div class="slide"><a href="$Link"><img src="$Image.FileName" alt="$Title.ATT" /></a></div>
<% end_control %>
</div>
<div id="AdvertisementsPreviousNext"></div>
<% end_if %>
