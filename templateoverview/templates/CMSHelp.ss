<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>HELP: $SiteTitle</title>
	<style type="text/css"><!--
  @media screen {
		@page {
			size: A4 landscape
			margin: 5%;
		}
		ul#HelpImages , #HelpImages li, #HelpImages img, #HelpImages h3 {padding-left: 0px; margin-left: 0px; float: none; border: none; padding: 0px!important; margin: 0px!important; text-align: left; display: block}
		#HelpImages li {page-break-before:always; list-style: none; border-top: 1px #555 dotted; margin-left: 0px; clear: both;}
		#HelpImages a img {border: 1px solid #e8e8e8;}
		#HelpImages a:hover img {border: 1px solid black;}
		#HelpImages a.small img {width: 33%; }
		#HelpImages a.big img {width: 93%;  }
		.backToTop {font-size:1.3em!important; }
		@media print {
			#HelpImages img {width: 100%!important;}
			.backToTop {display: none!important;}
			#HelpImages li {page-break-before:always; }
		}
	--></style>
</head>
<body class="typography">

	<h1>HELP: $SiteTitle</h1>

	<h2>General</h2>
	<p>General help with the Silverstripe Content Management System (CMS) is provided by <a href="http://userhelp.silverstripe.org/" target="_blank">Silvertripe Ltd</a>  (http://userhelp.silverstripe.org/).</p>
	<% if HelpFiles %>
	<h2 id="TOCHeading">Specific</h2>
	<p>Help specifically for <i>$SiteTitle</i>.</p>
	<ul id="TOC">
		<% control HelpFiles %>
		<li class="$EvenOdd $FirstLast"><a href="#Pos$Pos" class="small">$Title</a></li>
		<% end_control %>
	</ul>
	<ul id="HelpImages">
		<% control HelpFiles %>
		<li class="$EvenOdd $FirstLast" id="Pos$Pos">
			<h3><a class="backToTop" href="#TOCHeading">^</a> $Title</h3>
			<a href="#Pos$Pos" class="small"><img src="$Link" /></a>
		</li>

		<% end_control %>
	</ul>
	<% else %>
	<p>There are no help files.</p>
	<% end_if %>

	<script type="text/javascript"><!--
		jQuery(document).ready(
			function() {
				jQuery("#HelpImages a").click(
					function(event){
						if(jQuery(this).hasClass("big")) {
							jQuery(this).addClass("small");
							jQuery(this).removeClass("big");
						}
						else {
							jQuery(this).addClass("big");
							jQuery(this).removeClass("small");
						}
						//event.preventDefault()
					}
				);
				jQuery("a.backToTop").click(
					function(event){
						jQuery("#HelpImages a").each(
							function(i, el) {
								jQuery(el).removeClass("big");
								jQuery(el).addClass("small");
							}
						);
					}
				);
			}
		);
	--></script>
</body>

</html>


