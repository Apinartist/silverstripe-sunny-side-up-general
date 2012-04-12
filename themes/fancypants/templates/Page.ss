<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<% base_tag %>
		$MetaData
		$MetaTags
		<link rel="stylesheet" type="text/css" media="screen" href="$ThemeDir/css/core.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="$ThemeDir/css/typography.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="$ThemeDir/css/layout.css" />		
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" media="screen" href="$ThemeDir/css/ie.css" />
	<![endif]-->
	<!--[if lte IE 6]>
		<link rel="stylesheet" type="text/css" media="screen" href="$ThemeDir/css/ie6.css" />
	<![endif]-->
	</head>
	<body>
   		<div id="header" class="clearfix">
	   		<div id="nav">
	   			<% include MainNav %>
	   		</div>
	   		<div id="branding">
	   			<h1><a href="#">$SiteConfig.Title</a></h1>
	   			<h1 class="tagline">$SiteConfig.Tagline</h1>
	   		</div>		
   		</div>
    <div id="outerwrapper">
    <div id="wrapper">
	$Layout
   </div>
   </div>
    	<div id="footer" class="clearfix">
	    	<div id="credits">
		    	 <h3><a href="http://www.bluehousegroup.com?src=fancypantstheme">Theme By: Bluehouse Group</a></h3>
		    	 <p>This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.</p>
		    	 	    	</div>	    		
	    	<div id="footernav">
				<% include MainNav %>
	    	</div>
    	</div>
    
</body>
</html>
