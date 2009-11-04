<h3>review basic options</h3>
<p>Below is a list of options for reviewing orders:</p></p>
<div class="enterOrderNumber"><label>enter order number: <input name="ShopManagerPageOrderID" id="ShopManagerPageOrderID" /></label></div>
<ul id="ShopManagerPageOptionList">
	<li><a href="$Link/getorderdetailsforadmin">show order details</a></li>
	<li><a href="$Link/testorderreceipt">check email receipt</a></li>
	<li><a href="{$AccountPage.Link}/order">view confirmation page</a></li>
</ul>

<h3>Other options</h3>
<ul id="ShopManagerPageOtherOptions">
	<li><a href="$Link/clearcompletecart">clear complete cart</a> - useful if you want to pretend to be a new customer to the site</li>
</ul>
<% if LastOrders %>
<h3>Last Orders</h3>
<p class="showHideNext"><a href="#">show now</a></p>
<ul id="ShopManagerPageLastOrders">
	<% control LastOrders %><li>#$ID, $Created.Nice, $Status, $Member.Firstname $Member.Surname, $Member.Email</li><% end_control %>
</ul>
<% end_if %>