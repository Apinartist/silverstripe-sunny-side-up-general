<% control Cart %>
<div id="ShoppingCart">
	<h3><% _t("HEADLINE","My Cart") %></h3>
	<% if Items %>
		<ul>
			<% control Items %>
				<% if ShowInCart %>
					<li id="$CartID" class="$Classes">
						<span class="itemdetails">
							<span<% if Link %><% else %> id="$CartTitleID"<% end_if %> class="title">
								<% if Link %>
									<a id="$CartTitleID" href="$Link" title="<% sprintf(_t("READMORE","Click here to read more on &quot;%s&quot;"),$CartTitle.XML) %>">$CartTitle.XML</a>
								<% else %>
									$CartTitle.XML
								<% end_if %>
							</span>
						</span>
						<span class="remove">
							<strong>
								<a class="ajaxQuantityLink" href="$removeallLink" title="<% sprintf(_t("REMOVEALL","Remove all of &quot;%s&quot; from your cart"),$CartTitle.XML) %>">
									<img src="ecommerce/images/remove.gif" alt="x"/>
								</a>
							</strong>
						</span>
						<div class="clear"><!-- --></div>
					</li>
				<% end_if %>
			<% end_control %>
			<li class="subtotal"><% _t("SUBTOTAL","Subtotal") %>: <strong id="$CartSubTotalID">$SubTotal.Nice</strong></li>

			<li class="total"><% _t("TOTAL","Total") %>: <strong id="$CartTotalID">$Total.Nice $Currency</strong></li>
			<li class="buyProducts"><p><a class="checkoutButton" href="$checkoutLink" title="<% _t("CheckoutClick","Click here to go to the checkout") %>"><% _t("CheckoutGoTo","Go to checkout") %> &gt;&gt;</a></p></li>
		</ul>
	<% else %>
		<p class="noItems"><% _t("NOITEMS","There are no items in your cart") %>.</p>
	<% end_if %>
</div>
<% end_control %>