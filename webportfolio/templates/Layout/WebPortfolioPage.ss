<ul id="PortfolioItems">
<% control PortfolioItem %>
	<li class="$EvenOdd">
	<% control Screenshot.SetWidth(250) %>
		<a class="screenshotPopup" href="$Link"><img width="250" height="188" alt="$Title" src="$Link"/></a>
	<% end_control %>
		<div class="portFolioItem">
			<a href="#" class="webPortfolioShowMore">www.locatingsus.com</a>
			<div class="webPortfolioMoreInfo">
				<span>Screenshot taken: $ScreenshotTaken.Nice.</span>
				<span>Website for: $Client.</span>
	<% if WhatWeDid %>
				<span>Work completed:
			<% control WhatWeDid %><a href="$Link">$Name</a><% end_control %><% if Last %>.<% else %>,<% end_if %><% end_control %>
				</span>
	<% end_if %>
				<a href="http://www.locatingsus.com/" target="_blank" class="outsideLink">Please visit www.locatingsus.com</a></span>
			</div>
			<% if Agent %><span class="webPortfolioAgencySpan">Agency: <a href="AgentWebAddress.URL" class="outsideLink">$Name</a><% end_if %>
		</div>
	</li>
<% end_control %>
</ul>