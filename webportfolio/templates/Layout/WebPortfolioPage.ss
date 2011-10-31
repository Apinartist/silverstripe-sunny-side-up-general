<% if FilterList %>
<div id="Sidebar">
	<div class="sidebarBox filterList">
		<ul>
			<li class="<% if HasFilter %>link<% else %>current<% end_if %>"><a href="$Link">All</a></li>
	<% control FilterList %>
			<li class="$LinkingMode"><a href="$Link">$Name</a>
	<% if LinkingMode = current %><span>$Decription</span><% end_if %>
			</li>
	<% end_control %>
		</ul>
	</div>
</div>
<% end_if %>



<div id="MainContentSection">
	<h1 id="PageTitle">$Title</h1>
	$Content
</div>
<% if SelectedWebPortfolioItems %>
<ul id="WebPortfolioItems">
<% control SelectedWebPortfolioItems %>
	<li class="$EvenOdd $FirstLast">
		<a class="screenshotPopup" href="$Screenshot.Link" rel="prettyPhoto" title="website for $Client.ATT">
			<% control Screenshot.SetWidth(250) %><img width="250" height="188" alt="$Title.ATT" src="$Link"/><% end_control %>
		</a>
		<div class="portFolioItem">

			<span class="notes">
				<% if NoLongerAvailable %>
					<a href="#" class="webPortfolioShowMore" rel="WebPortfolioItem$ID">This site</a> is no longer available.
				<% else %>
					<% if NotPubliclyAvailable %>
					<a href="#" class="webPortfolioShowMore" rel="WebPortfolioItem$ID">This site</a> is not publicly available.
					<% else %>
					<a href="#" class="webPortfolioShowMore" rel="WebPortfolioItem$ID">$WebAddress</a>
					<% end_if %>
				<% end_if %>
				<% if Notes %> - $Notes<% end_if %>
			</span>

			<div class="webPortfolioMoreInfo" id="WebPortfolioItem$ID">

			<% if Client %>
				<span class="client"><strong>Client:</strong> $Client</span>
			<% end_if %>

			<% if Design %>
				<span class="design"><strong>Design:</strong> $Design</span>
			<% end_if %>

			<% if CodingFrontEnd %>
				<span class="coding"><strong>Interaction coding:</strong> $CodingFrontEnd</span>
			<% end_if %>

			<% if CodingBackEnd %>
				<span class="coding"><strong>Functionality coding:</strong> $CodingBackEnd</span>
			<% end_if %>

			<% if Copy %>
				<span class="copy"><strong>Copy:</strong> $Copy</span>
			<% end_if %>

			<% if Photography %>
				<span class="photography"><strong>Photography:</strong> $Photography</span>
			<% end_if %>


			<% if WhatWeDid %>
				<span class="whatWeDid"><strong>Work completed:</strong>
					<% control WhatWeDid %><a href="$Link"<% if Description %> title="$Description.ATT"<% end_if %>>$Name</a><% if Last %>.<% else %>, <% end_if %><% end_control %>
				</span>
			<% end_if %>

			<% if Agent %>
				<span class="agent"><strong>Agency:</strong> <% control Agent %><a href="AgentWebAddress.URL" class="externalLink">$Name</a><% end_control %></span>
			<% end_if %>

			<% if ScreenshotTaken %>
				<span class="screenshotTaken"><strong>Screenshot taken:</strong> $ScreenshotTaken.Year</span>
			<% end_if %>

			<span class="visit">
				<% if NoLongerAvailable %>
				<% else %>
					<% if NotPubliclyAvailable %>
					<% else %>
					<strong>Visit:</strong> <a href="$WebAddress.URL">$WebAddress.URL</a>
					<% end_if %>
				<% end_if %>
			</span>

			</div>

		</div>
	</li>
<% end_control %>
</ul>
<% end_if %>
<% if HasFilter %><p class="returnToNormal"><a href="$Link">show all items in <i>$MenuTitle</i></a></p><% end_if %>
$Form
$PageComments
