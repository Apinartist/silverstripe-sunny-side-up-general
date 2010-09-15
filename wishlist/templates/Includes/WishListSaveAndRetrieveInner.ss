<div class="wishListHolder"><% if WishListMessage %><span class="wishListMessage">$WishListMessage</span><% end_if %></div>
<ul class="wishListSaveAndRetrieveHolder">
	<% if CanSaveWishList %><li><a href="{$Link}savewishlist" class="wishListSave wishListSaveAndRetrieve">save current wish list  ($NumberOfItemsInSessionOnes)</a></li><% end_if %>
	<% if CanRetrieveWishList %><li><a href="{$Link}retrievewishlist" class="wishListRetrieve wishListSaveAndRetrieve">retrieve saved wish list ($NumberOfItemsInSavedOnes)</a></li><% end_if %>
	<% if CanClearWishList %><li><a href="{$Link}clearwishlist" class="wishListRetrieve">clear my wish lists</a></li><% end_if %>
</ul>
