<h2>Choose your download below:</h2>
$Content
<% control Downloads %>
<ul>
	<li>$Title
		<ul>
			<% if SVNLink %><li>SVN: <a href="$SVNLink">$SVNLink</a></li><% end_if %>
			<% if GITLink %><li>GIT: <a href="$GITLink">$GITLink</a></li><% end_if %>
			<% if DownloadLink %><li>Download (.zip): <a href="$DownloadLink">$DownloadLink</a></li><% end_if %>
		</ul>
	</li>
</ul>
<% end_control %>
