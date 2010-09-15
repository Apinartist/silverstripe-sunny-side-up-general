
<div id="PageResultHolder">
<% if Results %>
	<h1>Search Results</h1>

	<div id="Content">
		<% if Results.MoreThanOnePage %>
			<div id="Pages" class="first">
				<p>Page $Results.CurrentPage of $Results.TotalPages</p>

				<% if Results.NotLastPage %><a class="next" href="$Results.NextLink" title="View the next page">Next</a><% end_if %><% if Results.NotFirstPage %><a class="prev" href="$Results.PrevLink" title="View the previous page">Prev</a><% end_if %>
				<span>
		    	<% control Results.Pages %>
							<% if CurrentBool %>$PageNum<% else %><a href="$Link" title="View page number $PageNum">$PageNum</a><% end_if %>
					<% end_control %>
				</span>
			</div>
		<% end_if %>

		<ul id="SearchResults">
			<% control Results %>
				<li class="$EvenOdd">
					<h3><% if MenuTitle %> $MenuTitle<% else %>$Title<% end_if %></h3>
					<p>$Content.LimitWordCountPlainText</p>
					<a href="$Link" title="Read more about $Title">Read more &gt;&gt;</a>
				</li>
			<% end_control %>
		</ul>

		<% if Results.MoreThanOnePage %>
			<div id="Pages">
				<% if Results.NotLastPage %>
				<a class="next" href="$Results.NextLink" title="View the next page">Next</a><% end_if %><% if Results.NotFirstPage %><a class="prev" href="$Results.PrevLink" title="View the previous page">Prev</a><% end_if %>
				<span>
		    	<% control Results.Pages %>
							<% if CurrentBool %>$PageNum<% else %><a href="$Link" title="View page number $PageNum ">$PageNum</a><% end_if %>
					<% end_control %>
				</span>
				<p>Page $Results.CurrentPage of $Results.TotalPages</p>
			</div>
		<% end_if %>
	</div>
<% else %>
<p id="NoSearchResults">Sorry, no results were found, please try a different keyword phrase.</p>
<% end_if %>
</div>
