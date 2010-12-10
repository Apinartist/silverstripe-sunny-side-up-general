<table id="InformationTable" summary="<% _t("TABLESUMMARY","The contents of your cart are displayed in this form and summary of all fees associated with an order and a rundown of payments options.") %>">

	<thead>
		<tr class="gap">
			<td colspan="4" scope="row" class="left ordersummary"><h3><% _t("ORDERINFO","Information for Order #") %>{$ID}:</h3></td>
		</tr>
	</thead>

	<tbody>
<% include OrderInformation_MemberSection %>
	</tbody>
</table>
