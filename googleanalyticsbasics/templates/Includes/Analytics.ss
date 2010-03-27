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
<div style="background-color: yellow; height: 100px; color: red; clear: both; margin: 0; padding: 10px; text-align: center;">
Please note: this site is currently undergoing maintenance.
<% if CurrentMember %>Welcome Back, $CurrentMember.FirstName.
	<% if CurrentMember.IsAdmin %>
	Please <a href="/admin/show/$ID" style="color: red;">edit this page in CMS.</a>
	<% end_if  %>
<% else  %>
	You are not logged in.
<% end_if  %>
</div>
<% end_if %>