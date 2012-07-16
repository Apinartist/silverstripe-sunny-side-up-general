<!doctype html>
<!--[if lt IE 7]> <html class=" ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class=" ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class=" ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
	<% base_tag %>
	$ExtendedMetatags
</head>
<body id="Body$ClassName">
<div id="Wrapper">
	<div id="Container">
		<% include Navigation %>
 		<div id="Layout" class="typography">
			<% include Breadcrumbs %>
			<div id="LayoutHolder">$Layout</div>
			<div id="Footer">
				<span class="backToTop"><a href="#Wrapper">back to top</a></span>
				<div id="CopyrightMessage">$SiteConfig.CopyrightNotice</div>
			</div>
		</div>
	</div>
	<% include ShareThis %>
</div>
<% include TemplateOverviewPageDevelopmentFooter %>
<% include Analytics %>
</body>
</html>
