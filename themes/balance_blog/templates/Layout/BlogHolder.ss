<div class="typography">
<h1>Blog</h1>
<h2>You can easily add a blog to your website.</h2>
<p style="text-align: center; padding: 0 100px">The blog is easily customised and includes monthly and yearly archives, Akismet spam protection, comment moderation, and RSS feeds.</p>
</div>

<% include BlogSideBar %>

<div id="BlogContent" class="blogcontent typography">
	
	<% include BreadCrumbs %>
	
	<% if Tag %>
		<h3><% _t('VIEWINGTAGGED', 'Viewing entries tagged with') %> '$Tag'</h3>
	<% end_if %>
	
	<% if BlogEntries %>
		<% control BlogEntries %>
				<% include BlogSummary %>
		<% end_control %>
	<% else %>
		<h3><% _t('NOENTRIES', 'There are no blog entries') %></h3>
	<% end_if %>
	
	<% include BlogPagination %>
	
</div>
