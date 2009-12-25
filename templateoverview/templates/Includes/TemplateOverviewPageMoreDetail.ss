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
			<% if Image1 %><img src="$Image1.URL" alt="first image" style="border: 10px solid #ccc;" /><% end_if %>
		</div>
	</li>
	<% end_control %>
<% end_if %>

