$Content
<% if WebPortfolioItems %>
<ul id="WebPortfolioItems">
<% control WebPortfolioItems %>
	<li class="$EvenOdd $FirstLast">
		<a class="screenshotPopup" href="$Screenshot.Link" rel="prettyPhoto" title="$Client.ATT">
			<% control Screenshot.SetWidth(250) %><img width="250" height="188" alt="$Title.ATT" src="$Link"/><% end_control %>
		</a>
		<div class="portFolioItem">

			<div class="webPortfolioMoreInfo">


			<% if Client %>
				<span class="client">
					<strong>Website for:</strong>
				<% if NoLongerAvailable %>
					$Client - site no longer available
				<% else %>
					<% if NotPubliclyAvailable %>
					$Client - this site is not publicly available
					<% else %>
					<a href="$WebAddress.URL" class="webPortfolioShowMore">$Client</a>
					<% end_if %>
				</span>
				<% end_if %>

			<% end_if %>

			<% if Design %>
				<span class="design"><strong>Design:</strong> $Design</span>
			<% end_if %>

			<% if Coding %>
				<span class="coding"><strong>Coding:</strong> $Coding</span>
			<% end_if %>

			<% if Copy %>
				<span class="copy"><strong>Copy:</strong> $Copy</span>
			<% end_if %>

			<% if Photography %>
				<span class="photography"><strong>Photography:</strong> $Photography</span>
			<% end_if %>


			<% if WhatWeDid %>
				<span class="whatWeDid"><strong>Work completed:</strong>
					<% control WhatWeDid %><a href="$Link">$Name</a><% if Last %>.<% else %>,<% end_if %><% end_control %>
				</span>
			<% end_if %>

			<% if Agent %>
				<span class="agent"><strong>Agency:</strong> <% control Agent %><a href="AgentWebAddress.URL" class="externalLink">$Name</a><% end_control %></span>
			<% end_if %>


			<% if ScreenshotTaken %>
				<span class="screenshotTaken"><strong>Screenshot taken:</strong> $ScreenshotTaken.Nice</span>
			<% end_if %>

			</div>


		<% if Notes %>
			<span class="notes">$Notes</span>
		<% end_if %>

		</div>
	</li>
<% end_control %>
</ul>
<% end_if %>
$Form
$PageComments
