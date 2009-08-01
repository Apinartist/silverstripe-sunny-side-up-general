<% if GoogleAnalyticsCode %>
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	try {
		var pageTracker = _gat._getTracker("{$googleAnalyticsCode}");
		pageTracker._trackPageview();
	}
	catch(err) {}
</script>
<% else %>
<div style="background-color: yellow; height: 100px; color: red; clear: both; width: 100%; margin: 0; padding: 0; text-align: center;">this site is currently undergoing maintenance</div>
<% end_if %>