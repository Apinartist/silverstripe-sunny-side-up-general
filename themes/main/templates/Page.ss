<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<% base_tag %>
	$ExtendedMetatags
</head>
<body id="body$ClassName">
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
