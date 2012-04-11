
<div id="PageResultHolder">
<% if Results %>
	<h1>Search Results</h1>

	<div id="Content">
		<% if Results.MoreThanOnePage %>
			<div class="pageNumbers topPageNumbers">
				<span class="numberOfPages">Page $Results.CurrentPage of $Results.TotalPages</span>
				<span class="pageNumbers">
					Go to page:
		    	<% control Results.Pages %>
							<% if CurrentBool %>$PageNum<% else %><a href="$Link" title="View page number $PageNum">$PageNum</a><% end_if %>
					<% end_control %>
				</span>
			</div>
		<% end_if %>

		<ul id="SearchResults">
			<% control Results %>
				<li class="$EvenOdd">
					<h3><a href="$Link">$Title</a></h3>
					<p>$Content.Summary</p>
				</li>
			<% end_control %>
		</ul>

		<% if Results.MoreThanOnePage %>
			<div class="pageNumbers bottomPageNumbers">
				<span class="numberOfPages">Page $Results.CurrentPage of $Results.TotalPages</span>
				<span  class="pageNumbers">
					Go to page:
		    	<% control Results.Pages %>
							<% if CurrentBool %>$PageNum<% else %><a href="$Link" title="View page number $PageNum ">$PageNum</a><% end_if %>
					<% end_control %>
				</span>
			</div>
		<% end_if %>
	</div>
<% else %>
<p id="NoSearchResults">Sorry, no results were found, please try a different keyword phrase.</p>
<% end_if %>
</div>
