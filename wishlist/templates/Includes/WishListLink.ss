
<div class="addToWishListHolder">
<% if WishListMessage %><span class="wishListMessage">$WishListMessage</span><% end_if %>
<% if IsOnWishList %>
	<a href="{$Link}removefromwishlist/$ID/" class="removeToWishListLink wishListLink">remove from wish list</a>
<% else %>
	<a href="{$Link}addtowishlist/$ID/" class="addToWishListLink wishListLink">add to wish list</a>
<% end_if %>
</div>