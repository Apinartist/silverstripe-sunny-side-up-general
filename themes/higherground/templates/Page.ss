<!--
design by styleshout(http://www.styleshout.com/)
adapted to SilveStripe by SilverStriped (http://silverstriped.com)
-->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

  <head>
		<% base_tag %>
		$MetaTags
		<link rel="shortcut icon" href="/favicon.ico" />
	</head>
<body>
<div id="wrap">
	<div id="top-bg"></div>
	<div id="header">			
				
		<h1 id="logo-text"><a href="index.html" title="">higher<span>ground</span></a></h1>		
		<p id="slogan">put your site slogan here...</p>		
			
		<div id="header-links">
		<p>
			<a href="index.html">Home</a> | 
			<a href="index.html">Contact</a> | 
			<a href="index.html">Site Map</a>			
		</p>		
		</div>						
	</div>
		
	<div id="header-photo"></div>		

	<div  id="nav">
		<% include Navigation %>
	</div>					
	<div id="content-wrap">
	
		<div id="main">
				
			$Layout
			
						
			<br />	

		</div>
				
		<div id="sidebar">
			<% include Navigation2 %>
		</div>		
			
	</div>
		
	<div id="footer-wrap">
		<div id="footer-columns">
	
			<div class="col3">
				<h3>Tincidunt</h3>
				<ul>
					<li><a href="index.html">consequat molestie</a></li>
					<li><a href="index.html">sem justo</a></li>
					<li><a href="index.html">semper</a></li>
					<li><a href="index.html">magna sed purus</a></li>
					<li><a href="index.html">tincidunt</a></li>
				</ul>
			</div>

			<div class="col3-center">
				<h3>Sed purus</h3>
				<ul>
					<li><a href="index.html">consequat molestie</a></li>
					<li><a href="index.html">sem justo</a></li>
					<li><a href="index.html">semper</a></li>
					<li><a href="index.html">magna sed purus</a></li>
					<li><a href="index.html">tincidunt</a></li>
				</ul>
			</div>

			<div class="col3">
				<h3>Praesent</h3>
				<ul>
					<li><a href="index.html">consequat molestie</a></li>
					<li><a href="index.html">sem justo</a></li>
					<li><a href="index.html">semper</a></li>
					<li><a href="index.html">magna sed purus</a></li>
					<li><a href="index.html">tincidunt</a></li>					
				</ul>
			</div>

		</div>	
	
		<div id="footer-bottom">		
			
			<% include Footer %>
			
		</div>	

<!-- footer ends-->
</div>
<!-- wrap ends here -->
</div>

</body>
</html>
