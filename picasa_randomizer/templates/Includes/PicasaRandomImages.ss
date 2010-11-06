<% if PicasaRandomImage %>
<div id="PicasaRandomImages">
	<% control PicasaRandomImages %>
		<img src="$ThumbnailURL" alt="$Title" />
	<% end_controler %>
</div>
<% end_if %>
