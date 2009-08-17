<% include DropAndDragProductMenu %>

<div id="ProductGroup">
	<div id="Breadcrumbs" class="typography">
   	<p>$Breadcrumbs</p>
	</div>

	<h2 class="pageTitle">$Title</h2>

	<% if Content %>
		<div class="typography">
			$Content
		</div>
	<% end_if %>

	<% if DropAndDragProducts %>
		<h3 class="categoryTitle">Items</h3>
		<div id="FeaturedProducts" class="category">
			<ul class="productList">
				<% control DropAndDragProducts %>
					<% include DropAndDragProductGroupItem %>
				<% end_control %>
			</ul>
			<div class="clear"><!-- --></div>
		</div>
	<% else %>
	<p>There are no DropAndDrag items available</p>
	<% end_if %>

</div>