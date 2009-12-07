$Content
$Form
<div class="StockObjectsFeedback"><h1>Please enter new quantities below</h1></div>
<ul id="StockProductObjects">
<% control StockProductObjects %>
	<li>
		<label class="history"><a href="{$StockControlPage.Link}history/product/$ID" rel="history{$ClassName}{$ID}">$Title</a></label>
		<input type="text" value="0" name="$ClassName/$ID" class="updateField productUpdateField" />
		<label class="right">$CalculatedQuantity</label>
		<div class="loadHistoryHere" id="history{$ClassName}{$ID}"></div>
		<% if VariationQuantities %><ul id="StockVariationObjects"><% control VariationQuantities %>
			<li>
				<label class="history"><a href="{$StockControlPage.Link}history/variation/$ID" rel="history{$ClassName}{$ID}">$Title</a></label>
				<input type="text" value="0" name="$ClassName/$ID" class="updateField variationUpdateField" />
				<label class="right">$CalculatedQuantity</label>
				<div class="loadHistoryHere" id="history{$ClassName}{$ID}"></div>
			</li>
		<% end_control %></ul><% end_if %>
	</li>
<% end_control %>
</ul>
<div class="StockObjectsFeedback"><h1>Please enter new quantities above</h1></div>
