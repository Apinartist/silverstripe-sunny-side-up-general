<% control GoogleMapController %>
	<% if getShowStaticMapFirst %>
	<div class="loadInteractiveMap">
		<a href="#" onclick="loadMapScripts(); return false;">load interactive map</a>
	</div>
	<% end_if %>
	<% if getAddAddressFinder %>
	<div class="findAddressHtml">
		<a class="searchForAddress" href="#map" onclick="jQuery('#googleMapAddressFinderFieldSet').slideToggle(); return false;">search for address/location</a>
		<fieldset style="display: none;" id="googleMapAddressFinderFieldSet">
			<input type="text" class="infoTabInputAddress" id="mapAddress2" size="60" value="Enter your address or location" onclick="this.value = '';"/>
			<input type="button" class="submitButton" id="infoTabSubmitAddress2" value="find locations around your address"/>
		</fieldset>
	</div>
	<% end_if %>

  <div id="map" style="width: {$getGoogleMapWidth}px; height: {$getGoogleMapHeight}px;">
	 <% if getShowStaticMapFirst %><a href="#" id="loadMapNowWithinMap" onclick="loadMapScripts(); return false;">$dataPointsStaticMapHTML</a><% end_if %>
	</div>

	<div id="extraMapOptions typography">
   <div id="MapControlsOutsideMap" class="MapExtraInformation">
    <a href="#map" onclick="return !savePosition();">save map position</a> |
    <a href="#map" onclick="return !goToSavedPosition();">back to saved map position</a> |
    <a href="#map" onclick="initiateGoogleMap(); return false;">reset map</a> |
    <a href="#map" onclick="return !turnOnStaticMaps(this, '{$URLSegment}')" title="without loading dynamic maps (stop maps) - pages load faster">stop maps</a>
   </div>
   <% if getTitleDivId %><h4 id="$getTitleDivId" class="MapExtraInformation"></h4><% end_if %>
   <% if getLayerListDivId %><div id="$getLayerListDivId" class="MapExtraInformation"></div><% end_if %>
   <% if getSideBarDivId %>
	 <div id="$getSideBarDivId" class="MapExtraInformation">
		<% if dataPointsObjectSet %>
		<ul>
		<% control orderItemsByLatitude %>
				<li>$Title $AjaxInfoWindowLink</li>
		<% end_control %>
		</ul>
		<% end_if %>
	 </div>
	 <% end_if %>
   <% if getDropDownDivId %><div id="$getDropDownDivId" class="MapExtraInformation"></div><% end_if %>
   <% if getDirectionsDivId %><div id="$getDirectionsDivId" class="MapExtraInformation"></div><% end_if %>
   <% if getStatusDivId %><div id="$getStatusDivId" class="MapExtraInformation"></div><% end_if %>
  </div>
<% end_control %>