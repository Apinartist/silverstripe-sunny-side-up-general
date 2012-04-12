<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
		<% base_tag %>
		$MetaTags
		<link rel="shortcut icon" href="/favicon.ico" />
	</head>
<body>
<!-- wrap starts here -->
<div id="wrap">
	<div id="header">
		<div id="header-content">	
			<h1 id="logo"><a href="./" title="">pixel<span class="gray">green</span></a></h1>	
			<h2 id="slogan">Put your site slogan here...</h2>		
			<% include Navigation %>	
		</div>
	</div>
	<div class="headerphoto"></div>		
	<!-- content-wrap starts here -->
	<div id="content-wrap"><div id="content">		
		<% include SideBar %>
		<div id="main">		
			$Layout					
		</div>					
		
	<!-- content-wrap ends here -->		
	</div></div>
<!-- footer starts here -->	
<% include Footer %>
<!-- footer ends here -->	
<!-- wrap ends here -->
</div>

</body>
</html>
