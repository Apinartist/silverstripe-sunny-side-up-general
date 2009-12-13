<% if MoreDetail %>
	<% control MoreDetail %>
	<li>
		<p>$Description ... <a href="admin/templates/$ClassName/$ID/edit">edit details</a></p>
		<div id="TemplateOverviewImages">
			<% if Image1 %>$Image1.SetWidth(770)<% end_if %>
			<% if Image2 %>$Image2.SetWidth(770)<% end_if %>
			<% if Image3 %>$Image3.SetWidth(770)<% end_if %>
		</div>
	</li>
	<% end_control %>
<% end_if %>

<% control Results %>
	<li><a href="$URLSegment">$Title</a></li>
<% end_control %>
