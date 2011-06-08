<% if Designers %>
<ul id="Designers">
	<% control Designers %>
	<li class="$FirstLast">
		<h3>$Name, $Location, $Rate</h3>
		<a href="$Image1.Link" rel="prettyPhoto[$Name]"><img src="$Image1.Link" alt="$Image1.Title" /></a>
		<a href="$Image2.Link" rel="prettyPhoto[$Name]"><img src="$Image2.Link" alt="$Image2.Title" /></a>
		<a href="$Image3.Link" rel="prettyPhoto[$Name]"><img src="$Image3.Link" alt="$Image3.Title" /></a>
		<hr />
	</li>
	<% end_control %>
</ul>
<% end_if %>