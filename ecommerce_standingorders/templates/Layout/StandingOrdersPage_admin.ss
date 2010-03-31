<% if AllStandingOrders %>
<h2>Instructions</h2>
<p>
	Click on the individual orders below to complete them (<em>complete now</em> link).
	The orders will open in a separate window/tab.
	Complete the order at hand and then come back to this page to complete the next order.
</p>
<p>
	The colour scheme shows you if the individual orders are to be completed in the:
</p>
<ul class="TestDraftOrderList">
	<li class="past">past: should have been completed before today</li>
	<li class="current">current: to be completed today</li>
	<li class="future">future: to be completed some time in the future</li>
</ul>

<h2>Display Options</h2>
<ul class="standingOrdersGeneralOptions">
	<li><a href="" class="showHideAllStandingOrders">show all details for each standing order</a> - details are hidden by default, you can click on individual standing order title to view its details</li>
	<li><a href="" class="showHideWithoutOutstanding">show standing orders without outstanding orders</a> - these are hidden by default</li>
	<li><a href="" class="showHideFutureOrders">show future orders</a> - these are hidden by default</li>
</ul>
<h2>Standing Orders</h2>
<ul class="standingOrderList">
	<% control AllStandingOrders %>
	<li class="standingOrdersMainListItem">
		<h2><a href="#StandingOrderDetails-$ID" rel="$ID" class="standingOrderLink <% if OutstandingAutomaticallyCreatedOrders %>standingOrderWithOutstanding<% else %>standingOrderWithoutOutstanding<% end_if %>" <% if First %>id="firstStandingOrderLink"<% end_if %>>Standing Order # $ID</a></h2>
		<div>
			<div id="StandingOrderDetails-$ID" class="standingOrderDetails"><% include StandingOrder_Content %></div>
			<% if OutstandingAutomaticallyCreatedOrders %>
			<ul class="DraftOrderList">
				<% control OutstandingAutomaticallyCreatedOrders %>
					<li class="$FuturePast">
						Date: $OrderDate.Long, Status: $Status<% if FuturePast = future %><% else %>, next: <a href="$LoadLink" class="completeStandingOrder">complete now</a><% end_if %>
					</li>
				<% end_control %>
			</ul>
			<% else %>
			<p>There are no &quot;<em>to be completed</em>&quot; orders associated with this Standing Order.</p>
			<% end_if %>
			<hr />
		</div>
	</li>
	<% end_control %>
<% else %>
<p>There are currently no <em>active</em> standing orders. If you were expecting some active orders here then please make sure they are <a href="/admin/standing-orders">made <em>active</em></a> in the CMS first.</p>
<% end_if %>