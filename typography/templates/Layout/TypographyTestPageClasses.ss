<h1 id="allclasses">Templates used on this website ($TotalCount): </h1>
<ul id="classList">
<% control ClassList %>
	<% if Count %>
	<li style="background-image: url({$Icon});">
		<a href="$URLSegment">$Count x $ClassName templates - example: <i>$Title</i></a>
		<br />Link: $FullLink
	</li>
	<% else %>
	<li style="background-image: url({$Icon});">
		There are no instances of $ClassName templates.
	</li>
	<% end_if %>
<% end_control %>