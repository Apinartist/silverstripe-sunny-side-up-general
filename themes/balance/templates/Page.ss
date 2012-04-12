<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

  <head>
		<% base_tag %>
		<title>$Title &raquo; Your Site Name</title>
		$MetaTags(false)
		<link rel="shortcut icon" href="/favicon.ico" />
		
		<% require themedCSS(layout) %> 
		<% require themedCSS(typography) %> 
		<% require themedCSS(form) %> 
		
		<!--[if IE 6]>
			<style type="text/css">
			 @import url($ThemeDir/css/ie6.css);
			</style> 
		<![endif]-->
		
		<!--[if IE 7]>
			<style type="text/css">
			 @import url($ThemeDir/css/ie7.css);
			</style> 
		<![endif]-->
	</head>
	
	<body>
	
	<% include Navigation %>	
	
	<div id="BgContainer">
		<div id="Container">

				<div id="Layout">
					$Layout
				</div>
		
		</div>
	</div>

	<% include Footer %>
	
	<div id="bottom">
		$SilverstripeNavigator 
	</div>
	
	</body>
	
</html>
