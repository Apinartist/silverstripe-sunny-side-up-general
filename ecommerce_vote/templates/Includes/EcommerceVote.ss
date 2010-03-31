<div class="addToEcommerceVote">
<% if HasEcommerceVote %>
	<span>voted</span>
<% else %>
	<a href="{$Link}addecommercevote/$ID/" class="addEcommerceVote">vote</a>
<% end_if %>
</div>