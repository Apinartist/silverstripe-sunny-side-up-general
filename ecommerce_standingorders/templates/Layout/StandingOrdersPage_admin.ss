<% if AllStandingOrders %>
<ul class="StandingOrderList">
	<% control AllStandingOrders %>
	<li>
		<h2><a href="#StandingOrderDetails-$ID" rel="$ID" class="standingOrderLink" <% if First %>id="firstStandingOrderLink"<% end_if %>>Standing Order # $ID</a></h2>
		<div id="StandingOrderDetails-$ID" class="standingOrderDetails">
			<% include StandingOrder_Content %>
			<% if OutstandingAutomaticallyCreatedOrders %>
			<h5>Orders to be completed for Standing Order # $ID</h5>
			<ul class="DraftOrderList">
				<% control OutstandingAutomaticallyCreatedOrders %>
					<li class="$FuturePast">
						Date: $OrderDate.Long, Status: $Status<% if FuturePast = future %><% else %>, next: <a href="$LoadLink" target="_ordercompletion">complete now</a><% end_if %>
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