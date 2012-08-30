<div id="Download" class="mainSection content-container withSidebar">
	<h2>Choose your download below:</h2>
	$Content
	<h3>Options</h3>
	<ul>
		<li>GIT: <a href="#GITSectionBrowse">Browse</a>, <a href="#GITSection">Sub-modules</a></li>
		<li>SVN: <a href="#SVNSectionBrowse">Browse</a>, <a href="#SVNSection">externals</a></li>
		<li><a href="#DownloadSection">Downloads</a></li>
	</ul>

	<div id="GITSectionBrowse" style="margin-top: 30px;">
		<h2>GIT Browse</h2>
		<ul>
	<% control Downloads %><% if GITLink %><li><a href="$GITLink">$Title</a></li><% end_if %><% end_control %>
		</ul>
	</div>

	<div id="GITSection" style="margin-top: 30px;">
		<h2>GIT Submodule Definition</h2>
		<p>Browse to the root of your Silverstripe GIT-based project. Then paste the lines you can copy below.</p>
		<pre style="white-space: pre;">
	<% control Downloads %><% if FolderPadded %>git submodule add $GITLinkGIT $Folder
	<% end_if %><% end_control %>
		</pre>
	</div>


	<div id="SVNSectionBrowse" style="margin-top: 30px;">
		<h2>SVN Browse</h2>
		<ul>
	<% control Downloads %><% if SVNLink %><li><a href="$SVNLink">$Title</a></li><% end_if %><% end_control %>
		</ul>
	</div>

	<div id="SVNSection" style="margin-top: 30px;">
		<h2>SVN Externals Definition</h2>
		<p>To set these, using the command line, browse to the root folder of your Silvestripe SVN-based project and type:</p>
		<pre>svn propedit svn:externals .</pre>
		<p>Then paste the lines you can copy below.</p>
		<pre style="white-space: pre;">
	<% control Downloads %><% if FolderPadded %>$FolderPadded $SVNLink
	<% end_if %><% end_control %>
		</pre>
		<p>To finalise type:</p>
		<pre>svn up</pre>
	</div>


	<div id="DownloadSection">
	<h2>Downloads</h2>
	<p>Only logged in users can download.</p>
	<ul>
<% control Downloads %><% if DownloadLink %><li>Download (.zip): <a href="$DownloadLink">$DownloadLink</a></li><% end_if %><% end_control %>
	</ul>
	</div>
</div>
