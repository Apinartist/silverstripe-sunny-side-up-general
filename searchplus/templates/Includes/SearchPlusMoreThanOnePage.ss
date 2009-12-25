<% if Results.MoreThanOnePage %>
<div id="Pages" class="first">
	<p>Page $Results.CurrentPage of $Results.TotalPages</p>
	<p class="pageNumbers">
	<% if Results.NotLastPage %><a class="next" href="$Results.NextLink" title="View the next page">Next</a><% end_if %>
	<% if Results.NotFirstPage %><a class="prev" href="$Results.PrevLink" title="View the previous page">Prev</a><% end_if %>
	<span class="numbers"><% control Results.Pages %><% if CurrentBool %>$PageNum<% else %><a href="$Link" title="View page number $PageNum">$PageNum</a><% end_if %><% end_control %></span>
</div>
<% end_if %>