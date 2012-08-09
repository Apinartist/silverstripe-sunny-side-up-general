<% if Children %>
<div class="sidebarBox" id="SidebarChildren">
	<h3>In the <i>$MenuTitle</i> section</h3>
	<ul>
		<% control Children %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
	</ul>
</div>
<% end_if %>

<% if Siblings %>
<div class="sidebarBox" id="SidebarSiblings">
	<h3>Related pages</h3>
	<% if Parent %><p class="parentPageInfo">Other pages in the <% control Parent %><a href="$Link">$Title</a><% end_control %> section.</p><% end_if %>
	<ul>
		<% control Siblings %><li class="$FirstLast $LinkingMode"><a href="$Link">$Title</a></li><% end_control %>
	</ul>
</div>
<% end_if %>

<% if SearchForm %>
<div class="sidebarBox" id="SidebarSearch">
	<h3>Search</h3>
	<div class="searchFormOuter">$SearchForm</div>
</div>
<% end_if %>


<% if RandomImage %>
<div class="sidebarBox" id="RandomImage">
	<h3>About Sunny Side Up</h3>
	<p>
		Sunny Side Up is a Wellington, Aotearoa / New Zealand based web development company.
		Our web-sites include:
	</p>
	<ul>
		<li><a href="http://www.ssmods.com/">ssmods.com</a>: our web-development services, </li>
		<li><a href="http://www.silverstripe-ecommerce.com/">silverstripe-ecommerce.com</a>: silverstripe e-commerce demo site, </li>
		<li><a href="http://www.silverstripe-webdevelopment.com/">silverstripe-webdevelopment.com</a>: test site for our Silverstripe modules , and</li>
		<li><a href="http://www.silverstripe-webdevelopment.com/">www.sunnysideup.co.nz</a>: our corporate site with a difference.</li>
	</ul>
	<p>
		For those of you who prefer pictures over boring websites, here is one:
	</p>
	<div id="RandomVisualThought" style="background-image: url($RandomImage.URL); width: 200px; height: 200px;" rel="$RandomImage.URL"></div>
</div>
<% end_if %>

