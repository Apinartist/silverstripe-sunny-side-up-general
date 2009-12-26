<% include SearchPlusSearchResultList %>
<% if Recommendations %>
<div id="Recommendations">
	<h3>Popular Pages for '$Query' search phrase</h3>
	<ul><% control Recommendations %><li><a href="$Link">$Title</a></li><% end_control %></ul>
</div>
<% end_if %>
<% if RecommendedSearchPlusSection %>
<ul id="RecommendedSearchPlusSection">
	<% control RecommendedSearchPlusSection %>
	<li>
		<h3>$Title</h3>
		<p>$Intro</p>
		<% if ParentPage %><% control ParentPage %><ul><% control Children %><li><a href="$Link">$Title</a></li><% end_control %></ul><% end_control %><% end_if %>
	</li>
	<% end_control %>
</ul>
<% end_if %>
