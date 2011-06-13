<% if Designers %>
<ul id="Designers">
	<% control Designers %>
	<li class="$FirstLast">
		<h3>$Name, $Location, $Rate</h3>
		<% if Notes %><p>$Notes</p><% end_if %>
		<a href="$Sample1.Link" rel="prettyPhoto[$Name]"><img src="$Sample1.Link" alt="$Sample1.Title" class="first" /></a>
		<a href="$Sample2.Link" rel="prettyPhoto[$Name]"><img src="$Sample2.Link" alt="$Sample2.Title" /></a>
		<a href="$Sample3.Link" rel="prettyPhoto[$Name]"><img src="$Sample3.Link" alt="$Sample3.Title"  class="last" /></a>
		<hr />
	</li>
	<% end_control %>
</ul>
<% end_if %>
