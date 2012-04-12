<div id="Sidebar" class="typography ecommerce">
	<div class="sidebarBox">
 		<h3>
			<% control Level(1) %>
				$Title
			<% end_control %>
  		</h3>
  		
  		<ul id="Menu2">
			<% control GroupsMenu %>
  	    		<% if Children %>
			  	    <li class="$LinkingMode"><a href="$Link" title="<% sprintf(_t("GOTOPAGE","Go to the %s page"),$Title.XML) %>" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
	  	    	<% else %>
		  			<li><a href="$Link" title="<% sprintf(_t("GOTOPAGE","Go to the %s page"),$Title.XML) %>" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
				<% end_if %>	  
	  		
	  			<% if LinkOrSection = section %>
	  				<% if ChildGroups %>
						<ul class="sub">
							<li>
								<ul class="roundWhite">
									<% control ChildGroups %>
										<li><a href="$Link" title="<% sprintf(_t("GOTOPAGE","Go to the %s page"),$Title.XML) %>" class="$LinkingMode levelb"><span>$MenuTitle.LimitCharacters(22)</span></a></li>
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
	<div class="sidebarBottom"></div>
	
	<div class="sidebarBox cart">
		<% include Cart %>
	</div>
</div>