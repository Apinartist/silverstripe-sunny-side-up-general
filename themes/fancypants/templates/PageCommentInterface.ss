<div id="post-comments" class="section">
	<div class="pad clearfix">
		<% if Comments %>
			<h3>$Comments.Count Comments on &ldquo;$Page.Title&rdquo;</h3>
			<ul id="PageComments" class="clearfix">
				<% control Comments %>
					<li class="$EvenOdd<% if FirstLast %> $FirstLast <% end_if %> $SpamClass">
						<% include PageCommentInterface_singlecomment %>
					</li>
				<% end_control %>
			</ul>
			
			<% if Comments.MoreThanOnePage %>
				<div id="PageCommentsPagination">
					<p>
					<% if Comments.PrevLink %>
						<a href="$Comments.PrevLink">&laquo; <% _t('PREV','previous') %></a>
					<% end_if %>
					
					<% if Comments.Pages %>
						<% control Comments.Pages %>
							<% if CurrentBool %>
								<strong>$PageNum</strong>
							<% else %>
								<a href="$Link">$PageNum</a>
							<% end_if %>
						<% end_control %>
					<% end_if %>
	
					<% if Comments.NextLink %>
						<a href="$Comments.NextLink"><% _t('NEXT','next') %> &raquo;</a>
					<% end_if %>
					</p>
				</div>
			<% end_if %>
		<% else %>
			<h3>Comments on &ldquo;$Page.Title&rdquo;</h3>
			<p id="NoComments"><% _t('NOCOMMENTSYET','No one has commented on this page yet.') %></p>
		<% end_if %>
		<!--<p id="CommentsRSSFeed">
			<a class="commentrss" href="$CommentRssLink"><% _t('RSSFEEDCOMMENTS', 'RSS feed for comments on this page') %></a> | 
			<a href="PageComment/rss" class="commentrss" title="<% _t('RSSVIEWALLCOMMENTS', 'View all Comments') %>"><% _t('RSSFEEDALLCOMMENTS', 'RSS feed for all comments') %></a>
		</p>-->
	</div>
</div>
<div id="post-comment-form" class="section">
	<div class="pad clearfix">
		<% if CanPostComment %>
			<h3>Have some thoughts? Leave a Comment:</h3>
			$PostCommentForm
		<% else %>
			<p>You can't post comments until you have logged in<% if PostingRequiresPermission %>, and that you have an appropriate permission level<% end_if %>. Please <a href="Security/login?BackURL={$Page.URLSegment}/" title="Login to post a comment">login by clicking here</a>.</p>
		<% end_if %>
	</div>
</div>