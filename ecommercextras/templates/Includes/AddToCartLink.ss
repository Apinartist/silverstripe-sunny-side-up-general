<span class="ajaxAddToCartLink">
	<a class="ajaxRemove <% if IsInCart %>show<% else %>doNotShow<% end_if %>" href="$removeLinkAjax" >Remove From Cart</a>
	<a class="ajaxAdd <% if IsInCart %>doNotShow<% else %>show<% end_if %>" href="$addLinkAjax">Add To Cart</a>
</span>