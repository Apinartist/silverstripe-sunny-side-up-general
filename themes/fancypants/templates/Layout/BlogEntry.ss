<div id="main" class="clearfix">
<div id="pagetitle"><h2><a href="$Parent.Link"><span>$Parent.Title</span></a></h2></div>
	<div id="primaryContent" class="clearfix">
		<div class="innerpad">
			<div class="blogEntry">
			<h2 class="postTitle">$Title</h2>
			<p class="authorDate"><% _t('POSTEDBY', 'Posted by') %> $Author.XML <% _t('POSTEDON', 'on') %> $Date.Long | <a href="#PageComments">$Comments.Count <% _t('COMMENTS', 'Comments') %></a></p>
			<% if IsWYSIWYGEnabled %>
				$Content
			<% else %>
				$ParsedContent
			<% end_if %>
			<% if TrackBacksEnabled %>
		<% include TrackBacks %>
	<% end_if %>
				<% if TagsCollection %>
				<p class="tags">
					<% _t('TAGS', 'Tags:') %> 
					<% control TagsCollection %>
						<a href="$Link" title="<% _t('VIEWALLPOSTTAGGED', 'View all posts tagged') %> '$Tag'" rel="tag">$Tag</a><% if Last %><% else %>,<% end_if %>
					<% end_control %>
				</p>
			<% end_if %>

	$PageComments
		</div>
	</div>
	</div>
	<div id="secondaryContent">
		<div class="innerpad">
			<% include BlogSideBar %>
		</div>
	</div>
</div>






