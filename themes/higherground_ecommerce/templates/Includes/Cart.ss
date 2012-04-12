	<% control Cart %>
		<div id="ShoppingCart">
			<h3>My Cart</h3>
			<% if Items %>
				<ul>
					<% control Items %>
						<li>
							<a href="$Link" title="Click here to read more on &quot;{$Title}&quot;">$Title</a> <a href="{$Link}removeall" title="Remove &quot;{$Title}&quot; from your cart" style="color:red;font-weight:bold;">(-)</a>
							<span class="price">Price: <strong>$Price.Nice</strong> (x <strong id="Cart_Item{$ProductID}_Quantity">$Quantity</strong>)</span>
						</li>
						
					<% end_control %>
					<li class="subtotal"><br />Subtotal: <strong id="Cart_Subtotal">$Subtotal.Nice</strong></li>
					<% if Shipping %>
						<li>Shipping: <strong id="Cart_ShippingCost">$Shipping.Nice</strong></li>
					<% end_if %>
					<% if TaxInfo.LineItemTitle %>
						<li>$TaxInfo.LineItemTitle: <strong id="Cart_TaxCost">$TaxInfo.Charge.Nice</strong></li>
					<% end_if %>
					<li class="total">Total: <strong id="Cart_GrandTotal">$Total.Nice $Currency</strong></li>
					<li class="buyProducts"><p><a class="checkoutButton" href="checkout" title="Click here to go to the checkout">Go to checkout</a></p></li>
				</ul>
			<% else %> 
				<p class="noItems">There are no items in your cart.</p>
			<% end_if %>
		</div>
	<% end_control %>

