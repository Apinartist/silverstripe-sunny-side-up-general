<div id="sidebar" >
			<% if Menu(2) %>
			<div class="sidebox">	
				<h1 class="clear"><% control Level(1) %>
				$Title
			<% end_control %>
			</h1>
				<ul class="sidemenu">
					<% control Menu(2) %>	  
  					<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode<% if First %> top<% end_if %>"><span>$MenuTitle</span></a></li>
   					<% end_control %>			
				</ul>	
			</div>
			<% end_if %>
			<% if Menu(3) %>
			<div class="sidebox">	
				<h1 class="clear"><% control Level(2) %>
				$Title
			<% end_control %>
			</h1>
				<ul class="sidemenu">
					<% control Menu(3) %>	  
  					<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode<% if First %> top<% end_if %>"><span>$MenuTitle</span></a></li>
   					<% end_control %>			
				</ul>	
			</div>
			<% end_if %>
			<div class="sidebox">
				<h1>Short About</h1>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec libero. Suspendisse bibendum. 
				Cras id urna. Morbi tincidunt, orci ac convallis aliquam, lectus turpis varius lorem, eu 
				posuere nunc justo tempus leo.</p>	
			</div>			
			
			<div class="sidebox">	
				<h1>Sponsors</h1>
				<ul class="sidemenu">
					<li><a href="http://www.silverstriped.com" class="top">SilverStriped</a></li>
					<li><a href="http://www.4templates.com/?aff=ealigam">4templates</a></li>
					<li><a href="http://store.templatemonster.com/?aff=ealigam">TemplateMonster</a></li>	
					<li><a href="http://www.fotolia.com/partner/114283">Fotolia.com</a></li>								
					<li><a href="http://www.text-link-ads.com/?ref=40025">Text Link Ads</a></li> 		
				</ul>
			</div>
			
			<div class="sidebox">	
			
				<h1>Wise Words</h1>
				<p>&quot;No man can live happily who regards himself alone; 
				who turns everything to his own advantage. You must live for
				others if you wish to live for yourself.&quot;</p>					
				<p class="align-right">- Seneca</p>
					
			</div>		
						
			<div class="sidebox">	
						
				<h1>Search Box</h1>	
				<form action="#" class="searchform">
					<p>
					<input name="search_query" class="textbox" type="text" />
  					<input name="search" class="button" value="Search" type="submit" />
					</p>			
				</form>			
				
			</div>
					
		</div>	