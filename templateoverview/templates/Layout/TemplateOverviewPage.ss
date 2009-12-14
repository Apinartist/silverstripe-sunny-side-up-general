<h1 id="allclasses">Templates used on this website ($TotalCount): </h1>
<ul id="classList">
<% control ListOfAllClasses %>
	<% if Count %>
	<li style="background-image: url({$Icon});">
		<span class="typo-heading">$Count x $ClassName - template</span>
		<% if ShowAll %>
		<span class="typo-fullLink"><a href="$FullLink">$FullLink - $Title</a></span>
		<% else %>
		<span class="typo-example"><em>example:</em> <a href="$URLSegment">$Title</a></span>
		<span class="typo-fullLink"><em>link:</em> <a href="$FullLink">$FullLink</a></span>
		<span class="typo-more"><em>more:</em> <a href="$TypoURLSegment/showmore/$ID" class="typo-seemore" rel="entry-for-$URLSegment">more examples and details (if any)</a></span>
		<ol id="entry-for-$URLSegment"><li style="display: none;">&nbsp;</li></ol>
		<% end_if %>
	</li>
	<% else %>
	<li style="background-image: url({$Icon});">There are no instances of $ClassName templates.</li>
	<% end_if %>
<% end_control %>
</ul>