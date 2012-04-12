<a name="TemplateInfo"></a>
			<h2>$Title</h2>
					
			<p class="post-info">Posted by $Author.XML</p>
				
			<p>$Content.Parse(BBCodeParser)</p>
				
			<p class="post-footer">		
			<a class="comments">Comments ($Comments.Count)</a> |				
			<span class="date">$Date.Long</span>	
			</p>
			<% if CurrentMember %><p><a href="$EditURL" id="editpost" title="Edit this post">Edit this post</a> | <a href="$Link(unpublishPost)" id="unpublishpost">Unpublish this post</a></p><% end_if %>
			$PageComments