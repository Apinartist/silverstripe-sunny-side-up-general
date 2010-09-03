<% if WishList %>
<ul id="WishListSaveAndRetrieve">
	<% if CanSaveWishList %><li><a href="{$Link}savewishlist">save current wish list</a></li><% end_if %>
	<% if CanRetrieveWishList %><li><a href="{$Link}retrievewishlist">retrieve saved wish list</a></li><% end_if %>
</ul>
<% end_if %>