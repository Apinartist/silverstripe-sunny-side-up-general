<div class="DropAndDragImage">
	<% if Product.Image %>
		$Product.Image.Thumbnail
	<% else %>
		<img src="ecommerce/images/productPlaceHolderThumbnail.gif" alt="no image available" />
	<% end_if %>
</div>