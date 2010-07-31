<% if PageHasBeenRatedByUser %>
<div class="pageRaterStars">
<% control CurrentUserRating %>
	<label class="starLabel">Rating:</label>
	<div class="stars">
		<div style="width: {$RoundedPercentage}%" class="stars-bg"></div>
		<img alt="$Stars stars" src="pagerater/images/stars.png" />
	</div>
<% end_control %>
</div>
<% else %>
<div class="pageRaterStars">
<% control PageRatingResults %>
	<label class="starLabel">Rating:</label>
	<div class="stars">
		<div style="width: {$RoundedPercentage}%" class="stars-bg"></div>
		<img alt="$Stars stars" src="pagerater/images/stars.png" title="be the first to rate &quot;$Parent.Title.ATT&quot;" />
		<span>be the first to rate this page</span>
	</div>
<% end_control %>
</div>
<% end_if %>
