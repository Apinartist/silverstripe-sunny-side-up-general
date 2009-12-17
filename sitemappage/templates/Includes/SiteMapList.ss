<ul id="SiteMap">
<% control Menu(1) %>
	<li class="$FirstLast SiteMapLevel1">
		<% if SiteMapPages %><a href="#" class="siteMapPageExpander SiteMapNodeImploded SiteMapLevel1" rel="$ID">+</a> <% end_if %>
		<a href="$Link" class="pageTitle SiteMapLevel1">$Title</a>
		<% if SiteMapPages %>
		<ul id="sublist{$ID}"><% control SiteMapPages %>
			<li class="$FirstLast SiteMapLevel2">
				<% if SiteMapPages %><a href="#" class="siteMapPageExpander SiteMapNodeImploded SiteMapLevel2" rel="$ID">+</a> <% end_if %>
				<a href="$Link" class="pageTitle SiteMapLevel2">$Title</a>
				<% if SiteMapPages %>
				<ul id="sublist{$ID}"><% control SiteMapPages %>
					<li class="$FirstLast SiteMapLevel3">
						<% if SiteMapPages %><a href="#" class="siteMapPageExpander SiteMapNodeImploded SiteMapLevel3" rel="$ID">+</a> <% end_if %>
						<a href="$Link" class="pageTitle SiteMapLevel3">$Title</a>
						<% if SiteMapPages %>
						<ul id="sublist{$ID}"><% control SiteMapPages %>
							<li class="$FirstLast SiteMapLevel4">
								<% if SiteMapPages %><a href="#" class="siteMapPageExpander SiteMapNodeImploded SiteMapLevel4" rel="$ID">+</a> <% end_if %>
								<a href="$Link" class="pageTitle SiteMapLevel4">$Title</a>
								<% if SiteMapPages %><% control SiteMapPages %>
								<ul><li class="$FirstLast SiteMapLevel5"><a href="$Link" class="pageTitle SiteMapLevel5">$Title</a></li></ul>
								<% end_control %><% end_if %>
							</li><% end_control %>
						</ul><% end_if %>
					</li><% end_control %>
				</ul><% end_if %>
			</li><% end_control %>
		</ul><% end_if %>
	</li>
<% end_control %>
</ul>