<h1 id="allclasses">Templates used on this website ($TotalCount): </h1>
<ul id="classList">
<% control ListOfAllClasses %>
	<% if Count %>
	<li style="background-image: url({$Icon});">
		<span class="typo-heading">$Count x $ClassName - template</span>
		<span class="typo-example"><em>example:</em> <a href="$URLSegment">$Title</a></span>
		<span class="typo-fullLink"><em>link:</em> <a href="$FullLink">$FullLink</a></span>
		<% if AddShowMoreLink %>
		<span class="typo-more"><em>more:</em> <a href="$TypoURLSegment/showmore/$ID" class="typo-seemore" rel="entry-for-$URLSegment">see all</a></span>
		<ol id="entry-for-$URLSegment"><li>&nbsp;</li></ol>
		<% end_if %>
	</li>
	<% else %>
	<li style="background-image: url({$Icon});">There are no instances of $ClassName templates.</li>
	<% end_if %>
<% end_control %>
</ul>