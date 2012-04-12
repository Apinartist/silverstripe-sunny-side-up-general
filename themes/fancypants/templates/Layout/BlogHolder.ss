    	<div id="main" class="clearfix">
    	<div id="pagetitle"><h2><span>$Title</span></h2></div>
    		<div id="primaryContent" class="clearfix">
    			<div class="innerpad">
					<div id="BlogContent" class="blogcontent">
						
						<% include BreadCrumbs %>
						
						<% if SelectedTag %>
							<h3><% _t('VIEWINGTAGGED', 'Viewing entries tagged with') %> '$SelectedTag'</h3>
						<% else_if SelectedDate %>
							<h3><% _t('VIEWINGPOSTEDIN', 'Viewing entries posted in') %> $SelectedDate.Month $SelectedDate.Year</h3>
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

    			</div>
    		</div>
    		<div id="secondaryContent">
    			<div class="innerpad">
    			<% include BlogSideBar %>
    			</div>
    		</div>
    	</div>






