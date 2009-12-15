<% if MoreDetail %>
	<% control MoreDetail %>
	<li>
		<p>$Description ... <a href="admin/templates/$ClassName/$ID/edit">edit details</a></p>
		<div id="TemplateOverviewImages">
			<% if Image1 %><img src="$Image1.URL" alt="first image" /><% end_if %>
			<% if Image2 %><img src="$Image2.URL" alt="first image" /><% end_if %>
			<% if Image3 %><img src="$Image3.URL" alt="first image" /><% end_if %>
		</div>
	</li>
	<% end_control %>
<% end_if %>

<% control Results %>
	<li><a href="$URLSegment">$Title</a></li>
<% end_control %>