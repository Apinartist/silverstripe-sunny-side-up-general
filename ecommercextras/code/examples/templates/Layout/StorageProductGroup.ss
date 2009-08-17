<% include StorageProductMenu %>

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

	<% if StorageProducts %>
		<h3 class="categoryTitle">Items</h3>
		<div id="FeaturedProducts" class="category">
			<ul class="productList">
				<% control StorageProducts %>
					<% include StorageProductGroupItem %>
				<% end_control %>
			</ul>
			<div class="clear"><!-- --></div>
		</div>
	<% else %>
	<p>There are no storage items available</p>
	<% end_if %>

</div>