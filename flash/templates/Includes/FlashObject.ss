<% control FlashObjectData %>
	<% if UseDynamicInsert %>
    <div id="$ID">
      <p>$AlternativeContent</p>
    </div>
	<% else %>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$Width" height="$Height" id="$ID">
	<param name="movie" value="$FileName" />
	$Parameters
	<!--[if !IE]>-->
	<object type="application/x-shockwave-flash" data="$FileName" width="$Width" height="$Height">
		$Parameters
	<!--<![endif]-->
		<p>$AlternativeContent</p>
	<!--[if !IE]>-->
	</object>
	<!--<![endif]-->
</object>
	<% end_if %>
<% end_control %>