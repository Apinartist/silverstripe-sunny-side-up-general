<!-- see https://cms.paypal.com/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_formbasics -->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	 <input type="hidden" name="cmd" value="_cart">
	 <input type="hidden" name="business" value="$BusinessEmail">
	 <input type="hidden" name="item_name" value="$ProductName">
	 <input type="hidden" name="item_number" value="$SubmittedFormID">
	 <input type="hidden" name="amount" value="$Amount">
	 <input type="hidden" name="first_name" value="$FirstName">
	 <input type="hidden" name="last_name" value="$Surname">
	 <input type="hidden" name="address1" value="$Address1">
	 <input type="hidden" name="address2" value="$Address2">
	 <input type="hidden" name="city" value="$City">
	 <input type="hidden" name="state" value="$State">
	 <input type="hidden" name="zip" value="$Zip">
	 <input type="hidden" name="email" value="$Email">
	 <input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif"alt="PayPal - The safer, easier way to pay online">
</form>
