<div class="info">
	<% if CommenterURL %>
		<span><a href="$CommenterURL">$Name.XML</a></span> &mdash;
		<strong>$Created.Format(M j), $Created.Format(Y) <span>($Created.Ago)</span></strong>
	<% else %>
		<span>$Name.XML</span> &mdash;
		<strong>$Created.Format(M j), $Created.Format(Y) <span>($Created.Ago)</span></strong>
	<% end_if %>
</div>
<div class="comment" id="PageComment_$ID">
	<% if bbCodeEnabled %>
		$ParsedBBCode	
	<% else %>
		$Comment.XML	
	<% end_if %>
</div>
<ul class="actionLinks clearfix">
	
	<% if ApproveLink %>
		<li><a href="$ApproveLink" class="approvelink"><% _t('APPROVE', 'approve comment') %></a>&nbsp;|</li>
	<% end_if %>
	<% if SpamLink %>
		<li>&nbsp;<a href="$SpamLink" class="spamlink"><% _t('ISSPAM','mark as spam') %></a>&nbsp;|</li>
	<% end_if %>
	<% if HamLink %>
		<li>&nbsp;<a href="$HamLink" class="hamlink"><% _t('ISNTSPAM','mark as not spam') %></a>&nbsp;|</li>
	<% end_if %>
	<% if DeleteLink %>
		<li class="last">&nbsp;<a href="$DeleteLink" class="deletelink"><% _t('REMCOM','remove comment') %></a></li>
	<% end_if %>
</ul>