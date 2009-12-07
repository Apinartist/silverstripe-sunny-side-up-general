<h2>history for $Name</h2>

<% if OrderEntries %>
<h3>Sales</h3>
<ul>
	<% control OrderEntries %>
	<li>
		<span class="date">$LastEdited.Nice:</span>
		<span class="quantity">$Quantity</span>
		<span class="order">sold in Order with number $OrderID</span>
		<span class="small"><% if IncludeInCurrentCalculation %>included in current calculations no longer relevant to calculations<% end_if %></span>
	</li>
	<% end_control %>
</ul>
<% else %>
<h3>There are no sales yet</h3>
<% end_if %>

<% if ManualUpdates %>
<h3>Manual Adjustments</h3>
<ul>
	<% control ManualUpdates %>
	<li>
		<span class="date">$LastEdited.Nice:</span>
		<span class="quantity">$Quantity</span>
		<span class="member">updated by $Member.FirstName $Member.Surname</span>
	</li>
	<% end_control %>
</ul>
<% else %>
<h3>There are no manual adjustments</h3>
<% end_if %>

