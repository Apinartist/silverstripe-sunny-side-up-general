<div id="SearchPlusPopularSearches">
	<h3>Search Phrases entered at least $CountMin times during the last $Days days</h3>
	<h4>By Popularity</h4>
	<table summary="most popular search phrases by count" class="graphTable">
		<tr>
			<% control DataByCount %>
			<td>
				$Title | <a href="admin/searchplus/SearchHistory/{$ParentID}/edit" class="addRecommendations">edit</a>
			</td>
			<td class="background"><div style="width: {$Width}%;" class="foreground">$Count</div></td></tr>
			<% end_control %>
		</tr>
	</table>
	<h4>By Name</h4>
	<table summary="most popular search phrases by title" class="graphTable">
		<tr>
			<% control DataByTitle %>
			<td class="title">$Title</td>
			<td class="background"><div style="width: {$Width}%;" class="foreground">$Count</div></td></tr>
			<% end_control %>
		</tr>
	</table>
</div>