<div class="storageImage">
<% if Image %>
	<a href="$Link" title="<% sprintf(_t("READMORE","Click here to read more on &quot;%s&quot;"),$Title) %>"><img src="$Image.Thumbnail.URL" alt="<% sprintf(_t("IMAGE","%s image"),$Title) %>" /></a>
<% else %>
	<a href="$Link" title="<% sprintf(_t("READMORE"),$Title) %>"><img src="ecommerce/images/productPlaceHolderThumbnail.gif" alt="<% sprintf(_t("NOIMAGE","Sorry, no product image for &quot;%s&quot;"),$Title) %>" /></a>
<% end_if %>
</div>