<li class="productItem<% if IsInCart %> isInCart<% end_if %>" rel="$ID">
	<% include DropAndDragProductImage %>
	<h3 class="productTitle"><a href="$Link" title="<% sprintf(_t("READMORE"),$Title) %>">$Title</a></h3>
	<p>Drag to cart to add</p>
</li>
