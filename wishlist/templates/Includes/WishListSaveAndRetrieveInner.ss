<div class="wishListHolder"><% if WishListMessage %><span class="wishListMessage">$WishListMessage</span><% end_if %></div>
<ul class="wishListSaveAndRetrieve">
	<% if CanSaveWishList %><li><a href="{$Link}savewishlist" class="wishListSave">save current wish list</a></li><% end_if %>
	<% if CanRetrieveWishList %><li><a href="{$Link}retrievewishlist" class="wishListRetrieve">retrieve saved wish list</a></li><% end_if %>
</ul>
