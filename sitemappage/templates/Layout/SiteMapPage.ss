<ul id="SiteMap">
<% control Menu(1) %>
	<li class="$FirstLast Level1">
		<% if SiteMapPages %><a href="#" class="siteMapPageExpander" rel="$ID"><img src="/sitemap/images/plusMinus.gif" /></a> <% end_if %>
		<a href="$Link">$Title</a>
		<% if SiteMapPages %>
		<ul id="sublist{$ID}"><% control SiteMapPages %>
			<li class="$FirstLast SiteMapLevel2">
				<% if SiteMapPages %><a href="#" class="siteMapPageExpander" rel="$ID"><img src="/sitemap/images/plusMinus.gif" /></a> <% end_if %>
				<a href="$Link">$Title</a>
				<% if SiteMapPages %>
				<ul id="sublist{$ID}"><% control SiteMapPages %>
					<li class="$FirstLast SiteMapLevel3">
						<% if SiteMapPages %><a href="#" class="siteMapPageExpander" rel="$ID"><img src="/sitemap/images/plusMinus.gif" /></a> <% end_if %>
						<a href="$Link">$Title</a>
						<% if SiteMapPages %>
						<ul id="sublist{$ID}"><% control SiteMapPages %>
							<li class="$FirstLast SiteMapLevel4">
								<% if SiteMapPages %><a href="#" class="siteMapPageExpander" rel="$ID"><img src="/sitemap/images/plusMinus.gif" /></a> <% end_if %>
								<a href="$Link">$Title</a>
								<% if SiteMapPages %><% control SiteMapPages %>
								<ul><li class="$FirstLast SiteMapLevel5"><a href="$Link">$Title</a></li></ul>
								<% end_control %><% end_if %>
							<li><% end_control %>
						</ul><% end_if %>
					<li><% end_control %>
				</ul><% end_if %>
			</li><% end_control %>
		</ul><% end_if %>
	</li>
<% end_control %>
</ul>
