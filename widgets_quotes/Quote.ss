<div id="WidgetQuoteInner">
	<% if SubTitle %><p id="WidgetQuoteSubTitle" class="quotes">$SubTitle</p><% end_if %>
	<% if Quote %><p id="WidgetQuoteQuote" class="quotes">$Quote</p><% end_if %>
	<% if PublishedIn %><p id="WidgetQuotePublishedIn" class="quotes">$PublishedIn</p><% end_if %>
	<% if ExtraPublishingInformation %><p id="WidgetQuoteExtraPublishingInformation" class="quotes">$ExtraPublishingInformation</p><% end_if %>
	<% if PersonQuoted %><p id="WidgetQuotePersonQuoted" class="quotes">$PersonQuoted</p><% end_if %>
	<% if Photo %><span id="WidgetQuotePhoto" class="quotes">$Photo.SetWidth(180)</span><% end_if %>
</div>
