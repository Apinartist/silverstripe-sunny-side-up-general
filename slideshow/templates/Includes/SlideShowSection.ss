<% if SlidesForShow %>
<div id="SlidesForShow">
	<% control SlidesForShow %><div class="slidesForShowSlide"><% if Link %><a href="$Link.URL"><% end_if %><img src="$Image.FileName" alt="$Title" /><% if Link %></a><% end_if %></div><% end_control %>
</div>
<div id="SlidesForShowPreviousNext"></div>
<% end_if %>