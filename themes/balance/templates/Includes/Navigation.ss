<!-- The CSS for navigation in this theme has been optimsed for the number of items on demo.silverstripe.com. Chances are you'll need to adapt it to best suit your site  -->


<div id="NavWrapper">

	<div id="Navigation">
		
		<div id="PrimaryWrapper">
			<ul id="Primary">
			 	<% control Menu(1) %>	  
			  		<li class="$LinkingMode $Section"><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>$MenuTitle.XML</span></a></li>
			   	<% end_control %>
			</ul>
		</div>

		<!-- Prevent the secondary nav appearing in the blog, forum and ecommerce modules  -->					
		<% if ClassName != BlogHolder %>
		<% if ClassName != BlogEntry %>
		<% if ClassName != Forum %>
		<% if ClassName != ForumHolder %>
		<% if ClassName != ProductGroup %>
		<% if ClassName != Product %>

			<% if Menu(2) %>

			<div id="SecondaryWrapper">
				
				<ul id="Secondary">
					
					<!-- Depending on the number of nav items you may need to tweak the width of spacer in the css  -->					
					<li id="spacer">&nbsp;</li>
				  				
					<% control Menu(2) %>
		  	    		<% if Children %>
					  	    <li class="$LinkingMode"><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
			  	    	<% else %>
				  			<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
						<% end_if %>	  
	  		
			  			<% if LinkOrSection = section %>
			  				<% if Children %>
								<ul class="sub">
									<li>
										<ul>
											<% control Children %>
												<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode levelb"><span>$MenuTitle.XML</span></a></li>
											<% end_control %>
										</ul>
									</li>
								</ul>
					 		 <% end_if %>
						<% end_if %> 
					</li> 
		  			<% end_control %>
		  		</ul>
			</div>

		<% end_if %>
		<% end_if %>
		<% end_if %>
		<% end_if %>
		<% end_if %>
		<% end_if %>
		<% end_if %>	
	</div>
</div>