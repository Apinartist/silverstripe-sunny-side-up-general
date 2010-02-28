<% if FlashObjectData %><% control FlashObjectData %>

	<% if YouTubeStyle %>
		<object width="{$Width}" height="{$Height}">
			<param name="movie" value="$FileName"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowScriptAccess" value="always"></param>
			$Parameters
			<embed src="$FileName" type="application/x-shockwave-flash" allowscriptaccess="always" width="{$Width}" height="{$Height}" allowfullscreen="true"></embed>
		</object>


	<% else %>

		<div id="wrapperFor-{$ID}" style="height: {$Height}px!important; width: {$Width}px!important;">
			<% if UseDynamicInsert %>
			<div id="$ID" style="height: {$Height}px!important; width: {$Width}px!important;">
				<p class="FlashAlternativeContent" style="display: none;">$AlternativeContent</p>
			</div>
			<% else %>
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$Width" height="$Height" id="$ID">
				<param name="movie" value="$FileName" />
				$Parameters
				<!--[if !IE]>-->
				<object type="application/x-shockwave-flash" data="$FileName" width="$Width" height="$Height">
					$Parameters
				<!--<![endif]-->
					<p class="FlashAlternativeContent" style="display: none;">$AlternativeContent</p>
				<!--[if !IE]>-->
				</object>
				<!--<![endif]-->
			</object>
			<% end_if %>
		</div>


	<% end_if %>

<% end_control %><% end_if %>