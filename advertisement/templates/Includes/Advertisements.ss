<% if FlashAd %>
<div id="AdvertisementsHolder">
<% control FlashAd %>
	<div class="slide"><a href="$Link"><img src="$Image.FileName" alt="$Title.ATT" /></a></div>
<% end_control %>
</div>
<div id="AdvertisementsPreviousNext"></div>
<% end_if %>
