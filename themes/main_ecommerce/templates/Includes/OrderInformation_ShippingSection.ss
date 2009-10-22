<% if UseShippingAddress %>
	<tr class="gap shippingDetails">
		<td colspan="4" class="left"><% _t("SHIPPINGDETAILS","Shipping Details") %></td>
	</tr>
	<tr class="summary shippingDetails">
		<th colspan="2" scope="row" class="threeColHeader"><% _t("NAME","Name") %></td>
		<td colspan="2" class="price">$ShippingName</td>
	</tr>
	<% if ShippingAddress %>
	<tr class="summary shippingDetails">
		<th colspan="2" scope="row" class="threeColHeader"><% _t("ADDRESS","Address") %></th>
		<td colspan="2" class="price">$ShippingAddress</td>
	</tr>
	<% end_if %>
	<% if ShippingAddress2 %>
	<tr class="summary shippingDetails">
		<th colspan="2" scope="row" class="threeColHeader"></td>
		<td colspan="2" class="price">$ShippingAddress2</td>
	</tr>
	<% end_if %>
	<% if ShippingCity %>
	<tr class="summary shippingDetails">
		<th colspan="2" scope="row" class="threeColHeader"><% _t("CITY","City") %></th>
		<td colspan="2" class="price">$ShippingCity</td>
	</tr>
	<% end_if %>
	<% if ShippingCountry %>
	<tr class="summary shippingDetails">
		<th colspan="2" scope="row" class="threeColHeader"><% _t("COUNTRY","Country") %></th>
		<td colspan="2" class="price">$findShippingCountry</td>
	</tr>
	<% end_if %>
<% end_if %>