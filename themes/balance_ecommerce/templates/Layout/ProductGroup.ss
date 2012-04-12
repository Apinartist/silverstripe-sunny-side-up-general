<h1 class="pageTitle">Ecommerce</h1>

<div class="typography">

	<% include ProductMenu %>

	<div id="ProductGroup">
		$Content
	
	<!-- <div id="Breadcrumbs" class="typography">
   	<p>$Breadcrumbs</p>
	</div> -->
	
	<% if FeaturedProducts %>
		<h3 class="categoryTitle">Featured $Title</h3>
		<div id="FeaturedProducts" class="category">
			<div class="resultsBar typography">
				<p class="resultsShowing">Showing <span class="firstProductIndex">1</span> to <span class="lastProductIndex">$FeaturedProducts.Count</span> of <span class="productsTotal">$FeaturedProducts.Count</span> products</p>
			</div>
			<div class="clear"><!-- --></div>
			<ul class="productList">
				<% control FeaturedProducts %>
					<% include ProductGroupItem %>
				<% end_control %>
			</ul>
			<div class="clear"><!-- --></div>
		</div>
	<% end_if %>
	
	<% if NonFeaturedProducts %>
		<h3 class="categoryTitle">Other $Title</h3>
		<div id="NonFeaturedProducts" class="category">
			<div class="resultsBar typography">
				<p class="resultsShowing">Showing <span class="firstProductIndex">1</span> to <span class="lastProductIndex">$NonFeaturedProducts.Count</span> of <span class="productsTotal">$NonFeaturedProducts.Count</span> products</p>
			</div>
			<div class="clear"><!-- --></div>
			<ul class="productList">
				<% control NonFeaturedProducts %>
					<% include ProductGroupItem %>
				<% end_control %>
			</ul>
			<div class="clear"><!-- --></div>
		</div>
	<% end_if %>
</div>

</div>

