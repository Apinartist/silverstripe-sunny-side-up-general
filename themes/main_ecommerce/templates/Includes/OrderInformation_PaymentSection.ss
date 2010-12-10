<% control Payments %>
	<tr class="gap">
		<td colspan="4" class="left"><% _t("PAYMENTINFORMATION","Payment Information") %></th>
	</tr>
	<tr class="summary">
		<th colspan="2" scope="row"  class="twoColHeader"><% _t("PAYMENTID","Payment ID") %></th>
		<td colspan="2" class="price">#$ID</td>
	</tr>
	<tr class="summary">
		<th colspan="2" scope="row" class="twoColHeader"><% _t("DATE","Date") %></th>
		<td colspan="2" class="price">$LastEdited.Nice</td>
	</tr>
	<tr class="summary">
		<th colspan="2" scope="row"  class="twoColHeader"><% _t("AMOUNT","Amount") %></th>
		<td colspan="2" class="price">$Amount.Nice $Currency</td>
	</tr>
	<tr class="summary">
		<th colspan="2" scope="row"  class="twoColHeader"><% _t("PAYMENTSTATUS","Payment Status") %></th>
		<td colspan="2" class="price">$Status</td>
	</tr>

	<% if PaymentMethod %>
	<tr class="summary">
		<th colspan="2" scope="row" class="twoColHeader"><% _t("PAYMENTMETHOD","Method") %></th>
		<td colspan="2" class="price">$PaymentMethod</td>
	</tr>
	<% end_if %>

	<% if Message %>
	<tr class="summary">
		<td colspan="4" class="left">$Message</td>
	</tr>
	<% end_if %>
<% end_control %>
