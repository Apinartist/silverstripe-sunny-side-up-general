<span class="ajaxAddToCartLink">
	<a class="ajaxRemove <% if IsInCart %>show<% end_if %>" href="$removeLinkAjax" >Remove From Cart</a>
	<a class="ajaxAdd <% if IsInCart %><% else %>doNotShow<% end_if %>" href="$addLinkAjax">Add To Cart</a>
</span>