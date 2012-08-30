<div id="Download" class="mainSection content-container withSidebar">
	<h2>Choose your download below:</h2>
	$Content
	<div id="VersionInfo" style="margin-top: 30px;">
	<h3>What Version should I use?</h3>
	<p>
		Below you see the TRUNK version.  Trunk is being used on this site.
		For any production site, we recommend using a branch (e.g. 1.0, 2.0, 3.0).
		Branches only get <i>patched</i> if community members report bugs.
		If you prefer a "never changing" tag then you can make your own by fixing a branch to a revision number or creating a github fork.
	</p>
	</div>

	<h3 style="margin-top: 30px;">GIT Hub</h3>
	<div id="GITSectionBrowse" style="margin-top: 30px;">
		<h4>Browse</h4>
		<ul>
<% control Downloads %><% if GITLink %><li><a href="$GITLink">$Title</a></li><% end_if %><% end_control %>
		</ul>
	</div>

	<div id="GITSection" style="margin-top: 30px;">
		<h2>Sub-module Definition</h2>
		<p>Browse to the root of your Silverstripe GIT-based project. Then paste the lines you can copy below.</p>
		<pre style="white-space: pre;956px!important">
<% control Downloads %><% if FolderPadded %>git submodule add $GITLinkGIT $Folder
<% end_if %><% end_control %>
		</pre>
	</div>

	<h3 style="margin-top: 30px;">SVN</h3>
	<div id="SVNSectionBrowse" style="margin-top: 30px;">
		<h4>Browse</h4>
		<ul>
<% control Downloads %><% if SVNLink %><li><a href="$SVNLink">$Title</a></li><% end_if %><% end_control %>
		</ul>
	</div>

	<div id="SVNSection" style="margin-top: 30px;">
		<h4>Externals Definition</h4>
		<p>To set these, using the command line, browse to the root folder of your Silvestripe SVN-based project and type:</p>
		<pre>svn propedit svn:externals .</pre>
		<p>Then paste the lines you can copy below.</p>
		<pre style="white-space: pre;956px!important">
<% control Downloads %><% if FolderPadded %>$FolderPadded $SVNLink
<% end_if %><% end_control %>
		</pre>
		<p>To finalise type:</p>
		<pre>svn up</pre>
	</div>


	<div id="DownloadSection" style="margin-top: 60px;">
	<h3>Downloads</h3>
	<p>Only <a href="Security/login/?BackURL=$Link.ATT">logged in</a> users can download.</p>
	<ul>
<% control Downloads %><% if DownloadLink %><li><a href="$DownloadLink">$Title</a></li><% end_if %><% end_control %>
	</ul>
	</div>
</div>

<aside>
	<div id="Sidebar">
		<div class="sidebarTop"></div>
		<div id="SidebarPageContent" class="sidebarBox">
			<h2>Contents</h2>
			<ul>
				<li><a href="#VersionInfo">What Version should I use?</a></li>
				<li>GIT: <a href="#GITSectionBrowse">Browse</a>, <a href="#GITSection">Sub-modules</a></li>
				<li>SVN: <a href="#SVNSectionBrowse">Browse</a>, <a href="#SVNSection">Externals</a></li>
				<li><a href="#DownloadSection">Downloads</a></li>
			</ul>
		</div>
		<% include Sidebar %>
		<div class="sidebarBottom"></div>
	</div>
</aside>
