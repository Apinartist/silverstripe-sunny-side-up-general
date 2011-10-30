<% if FilterList %>
<div id="Sidebar">
	<div class="sidebarBox filterList">
		<ul>
			<li class="<% if HasFilter %>link<% else %>current<% end_if %>"><a href="$Link">All</a></li>
	<% control FilterList %>
			<li class="$LinkingMode"><a href="$Link">$Name</a></li>
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
		<a class="screenshotPopup" href="$Screenshot.Link" rel="prettyPhoto" title="$Client.ATT">
			<% control Screenshot.SetWidth(250) %><img width="250" height="188" alt="$Title.ATT" src="$Link"/><% end_control %>
		</a>
		<div class="portFolioItem">

			<div class="webPortfolioMoreInfo">

			<span class="notes">
				<% if NoLongerAvailable %>
					This site no longer available.
				<% else %>
					<% if NotPubliclyAvailable %>
					This site is not publicly available.
					<% else %>
					<strong>Site:</strong> <a href="$WebAddress.URL" class="webPortfolioShowMore">$WebAddress</a> -
					<% end_if %>
				<% end_if %>
				<% if Notes %>$Notes<% end_if %>
			</span>


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

			</div>




		</div>
	</li>
<% end_control %>
</ul>
<% end_if %>
<% if HasFilter %><p class="returnToNormal"><a href="$Link">show all items for <i>$MenuTitle</i></a></p><% end_if %>
$Form
$PageComments
