<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<% base_tag %>
	<% require themedCSS(reset) %>
	<% require themedCSS(Order_PackingSlip) %>
	<title><% _t("PAGETITLE","Packing Slip") %></title>
</head>
<body>
	<div id="OrderInformation">
		<% control DisplayFinalisedOrder %>
		<div>
			<table class="packingSlip" summary="address details">

				<thead>
					<tr class="gap">
						<td colspan="4" scope="row" class="left ordersummary"></td>
						<td><h2>Packing Slip</h2></td>
					</tr>
				</thead>

				<tbody>
					<tr class="gap">
						<td colspan="5" scope="row" align="right">$Now.Long</th>
					</tr>
					<tr class="gap">
						<td colspan="3"></td>
						<td valign="top">Ship to:</td>
						<td>
							<address>
							<% if ShippingName %>
								$ShippingName<br />
								<% if ShippingAddress %>$ShippingAddress<br /><% end_if %>
								<% if ShippingAddress2 %>$ShippingAddress2<br /><% end_if %>
								<% if ShippingCity %>$ShippingCity<br /><% end_if %>
								<% if ShippingCountry %>$ShippingCountry<br /><% end_if %>
							<% else %>
								<% control Member %>
									$FirstName $Surname<br />
									$Address<br />
									$AddressLine2<br />
									$City<br />
									$Country<br />
								<% end_control %>
							<% end_if %>
							</address>
						</td>
					</tr>
				</tbody>

			</table>

			<table class="packingSlip orderDetails" summary="order details">
				<tbody>
					<tr class="orderDetails">
						<td colspan="3"><% _t("ORDERDATE","Order Date") %></td>
						<td><% _t("ORDERNUMBER","Order Number") %></td>
					</tr>
					<tr>
						<td colspan="3">$Created.Nice</td>
						<td>$ID</td>
					</tr>
				</tbody>
			</table>

			<table class="packingSlip orderDetails" summary="item details">
				<tbody>
					<tr class="orderDetails">
						<td colspan="3"><% _t("ITEM","Item") %> #</td>
						<td><% _t("DESCRIPTION","Description") %></td>
						<td><% _t("QUANTITY","Quantity") %></td>
					</tr>
					<% control Items %>
						<tr>
							<td colspan="3"><% if InternalItemID %>$InternalItemID<% else %>$ID<% end_if %></td>
							<td>$ProductTitle</td>
							<td>$Quantity</td>
						</tr>
					<% end_control %>
				</tbody>
			</table>

		</div>
		<% end_control %>
	</div>
</body>
</html>
