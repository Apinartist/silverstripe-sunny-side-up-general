<h1 id="allclasses">Templates used on this website ($TotalCount): </h1>
<ul id="classList">
<% control ListOfAllClasses %>
	<% if Count %>
	<li style="background-image: url({$Icon});">
		<% if TemplateOverviewDescription %><% control TemplateOverviewDescription %><% if Image1ID %><span class="thumb">$Image1.SetWidth(150)</span><% end_if %><% end_control %><% end_if %>
		<span class="typo-heading">$Count x $ClassName - template</span>
		<% if ShowAll %>
		<span class="typo-fullLink"><a href="$FullLink">$FullLink</a> :: $Title</span>
		<% else %>
		<span class="typo-fullLink"><em>example:</em> <a href="$FullLink">$FullLink</a> :: $Title</span>
		<span class="typo-more"><em>more:</em> <a href="$TypoURLSegment/showmore/$ID" class="typo-seemore" rel="entry-for-$URLSegment">more examples and details (if any)</a></span>
		<ol id="entry-for-$URLSegment" class="MoreDetailOL"><li style="display: none;">&nbsp;</li></ol>
		<% end_if %>
	</li>
	<% else %>
	<li style="background-image: url({$Icon});">There are no instances of $ClassName templates.</li>
	<% end_if %>
<% end_control %>
</ul>