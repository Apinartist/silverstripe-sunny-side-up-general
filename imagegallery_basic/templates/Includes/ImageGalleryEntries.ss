<% if ImageGalleryEntries %>
<div id="ImageGalleryEntriesOuter">
	<ul id="ImageGalleryEntriesUL">
	<% control ImageGalleryEntries %>
		<% if Image %><li class="$EvenOdd $FirstLast ImageGalleryEntry-$Pos"><% control Image.SetWidth(I00) %><img src="$Link" title="$Title" alt="$Title" width="100" /><% end_control %></li><% end_if %>
	<% end_control %>
	</ul>

</div>
<% end_if %>

