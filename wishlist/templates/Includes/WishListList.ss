<div class="wishListHolder"><% if WishListMessage %><span class="wishListMessage">$WishListMessage</span><% end_if %></div>
<% if WishList %>
<ul id="WishListList">
	<% control WishList %>
	<li><a href="$Link">$Title</a> - <a href="{$Link}removefromwishlist/$ID/" class="removeToWishListLink wishListLink">remove from wish list</a></li>
	<% end_control %>
</ul>
<% end_if %>