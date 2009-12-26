<h2 id="SearchPlusSearchResultListHeading">Search for <q>$Query</q></h2>
<% include SearchPlusMoreThanOnePage %>
<% if Results %>
<ul id="SearchResults">
<% control Results %>
	<li class="$EvenOdd">
		<h3><% if MenuTitle %> $MenuTitle<% else %>$Title<% end_if %></h3>
		<p>$Content.LimitWordCountPlainText</p>
		<a href="$Link" title="Read more about $Title.ATT">Read more &gt;&gt;</a>
	</li>
<% end_control %>
</ul>
<% else %>
<p class="SearchPlusSearchResultListRegret">Sorry, no pages matched your search, please try again.</p>
<% end_if %>
<% include SearchPlusMoreThanOnePage %>
