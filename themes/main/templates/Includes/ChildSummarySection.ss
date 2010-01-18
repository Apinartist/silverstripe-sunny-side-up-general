<div id="ChildSummarySection">
<% if Children %>
	<ul>
	<% control Children %>
		<li>
			<h2>$Title.XML</h2>
			<p>
				$Content.LimitWordCountXML
				<a href="$Link">Read More...</a>
			</p>
		</li>
	<% end_control %>
	</ul>
<% end_if %>
</div>