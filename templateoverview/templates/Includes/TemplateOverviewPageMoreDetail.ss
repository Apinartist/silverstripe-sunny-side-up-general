<% control Results %>
	<li>example #{$Pos}. <a href="$URLSegment">$Title</a> - <a href="/admin/show/$ID">CMS view</a></li>
<% end_control %>
<% if MoreDetail %>
	<% control MoreDetail %>
	<li>
		<p class="templateOverviewDescription">
			$Description ... <a href="admin/templates/$ClassName/$ID/edit">edit this description</a>
			<% if ToDoListHyperLink %> | <a href="$ToDoListHyperLink.URL" class="updateToDoListLink">update to do list</a><% end_if %>
		</p>
		<div id="TemplateOverviewImages">
				<% if Image1ID %><span class="mediumThumb"><a href="$Image1.URL" rel="prettyPhoto[$ClassNameLink]">$Image1.SetWidth(325)</a></span><% end_if %>
				<% if Image2ID %><span class="mediumThumb"><a href="$Image2.URL" rel="prettyPhoto[$ClassNameLink]">$Image2.SetWidth(325)</a></span><% end_if %>
				<% if Image3ID %><span class="mediumThumb"><a href="$Image3.URL" rel="prettyPhoto[$ClassNameLink]">$Image3.SetWidth(325)</a></span><% end_if %>
				<% if Image4ID %><span class="mediumThumb"><a href="$Image4.URL" rel="prettyPhoto[$ClassNameLink]">$Image4.SetWidth(325)</a></span><% end_if %>
				<% if Image5ID %><span class="mediumThumb"><a href="$Image5.URL" rel="prettyPhoto[$ClassNameLink]">$Image5.SetWidth(325)</a></span><% end_if %>
		</div>
	</li>
	<% end_control %>
<% end_if %>

