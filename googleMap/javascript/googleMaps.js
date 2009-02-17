/*
many thanks to : http://econym.googlepages.com/index.htm
created by www.sunnysideup.co.nz
to do:
	-. replace javascript(void) with a warning message that the command did not work.
	-. allow an icon to be anchored anywhere (right now it is at the center bottom)
	-. zoom to in xml does not work....
	-. all markers should have unique ID (e.g. addPointggg0)
	-. make all human added markers dragable
	-. implement this:
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Document>
			<Style id="randomColorIcon">
						<IconStyle>
									<Icon>
												<href>http://maps.google.com/mapfiles/kml/pal3/icon21.png</href>
									</Icon>
						</IconStyle>
			</Style>
			<Placemark>
						<name>IconStyle.kml</name>
						<styleUrl>#randomColorIcon</styleUrl>
						<Point>
									<coordinates>-122.36868,37.831145,0</coordinates>
						</Point>
			</Placemark>
</Document>
</kml>
1. if points has styleUrl
	2. find id for styleUrl
		3. get location from styleUrl
			4. replace icon URL with styleUrl
*/

var map = null;
var geocoder = null;
var directions = null;
var NZLongitude = '0.0001';//173.2210
var NZLatitude = '0.0001';//-41.2943
var NZZoom = 2;//5
var GMO;
var addedPoint = 0;

function loadMap() {
	if (GBrowserIsCompatible()) {
		GMO = new GMC("map", null,
			{
/* HELPDIVS */
				sideBarId:"sideBar",
				dropBoxId:"dropBox",
				titleId:"mapTitle",
				layerListId:"layerList",
				addKmlLink:true,
				directionsDivId:"directions",
				statusDivId:"status",
				noStatusAtAll:false,
				hiddenLayersRemovedFromList:false,
/* PAGE*/
				changePageTitle:true,
				defaultTitle:"Create a new search",
/* INFOWINDOW*/
				infoWindowOptions:{maxWidth:280, zoomLevel:17, mapType:G_HYBRID_MAP},
				addAntipodean:true,
				addDirections:true,
				addCustomSearchForm:true,
				customSearchFormName: "my content",
				customSearchFormHtml: "put your own custom content here",
/* MARKER AND ICONS (include title to have a title)*/
				addPointsToMap:true,
				markerOptions:{bouncy:true,title: "click me"},
				preloadImages:true,
				defaultIconUrl: "",
				iconFolder: "http://www.sunnysideup.co.nz/j/map/",
				iconWidth:20,
				iconHeight:34,
				iconExtension:"png",
				iconMaxCount:12,
/* POLYS */
				lineColour: "#dcb916",
				lineWidth: "5",
				lineOpacity: "0.5",
				fillColour: "#dcb916",
				fillOpacity: "0.3",
				polyIcon: "",
/* MAP*/
				mapTypeDefaultZeroToTwo: 0, // NOW UP TO THREE! normal, satellite, hybrid, terrain
				viewFinderSize:250,
				mapAddTypeControl:true,
				mapControlSizeOneToThree:3,
				mapScaleInfoSizeInPixels:100,
/* START POSITION*/
				defaultLatitude:false,
				defaultLongitude:false,
				defaultZoom:false,
/* SERVER INTERACTION AND ADDRESS SEARCH
					- updateServerUrlDragend sends data back to server when the marker is moved. URL should have ? in it as it sends data back as x=1233&y=2344
					- updateServerUrlAddPoint sends data back to server when a marker is added. URL should have ? in it as it sends data back as x=1233&y=2344
					- if latFormFieldId and lngFormFieldId are enabled, their value will be updated to the last marker created OR moved*/
				updateServerUrlDragend: "mapDBConnection.php?",
				updateServerUrlAddPoint: "mapDBConnection.php?",
				latFormFieldId:"Latitude",
				lngFormFieldId:"Longitude",
/* ADDRESS FINDER FORM */
				addAddressFinder:true,
				defaultCountryCode:"",
				defaultAddressText:"",
/* DIRECTIONS */
				styleSheetUrl: "googleMap/css/mapDirections.css",
				localeForResults: "en_NZ"
			}
		);
	}
}
function addLayer(url) {
	GMO.downloadXml(url);
	return true;
}
function addPoint(lat, lng, nameString, description) {
	var pointLngLat = lng + "," + lat;
	//name, pointLngLat, description, latitude, longitude, zoom, info
	GMO.processXml(GMO.createPointXml(nameString, pointLngLat, description));
}
function findAddress(address) {
	GMO.showAddress(address);
	return true;
}
function findRoute() {
	GMO.showRoute();
	return true;
}
function savePosition() {
	map.savePosition();
	GMO.updateStatus("Position saved.");
	return true;
}
function goToSavedPosition() {
	map.returnToSavedPosition();
	GMO.updateStatus("Returned to saved position.");
	return true;
}


function GMC(mapDivName, url, opts) {
	// store the parameters
	window.onunload = GUnload;
	this.opts = opts || {};
	this.mapDivName = mapDivName;
	this.mapIsZoomed = false;
	this.mapOriginalSize = {};
	this.defaultUrl = url || "";
	this.latestUrl = url || "";
	this.currentPageURL = location.href.replace(/#/g,""); //replaces all #
	// other useful "global" stuff
	this.gmarkers = [];
	this.layerInfo = [];
	this.lastMarker = {};
	this.markerImageCollection = [];
	this.imageNum = 0;
	if(!this.opts.defaultLatitude) {  this.opts.defaultLatitude = NZLatitude; }
	if(!this.opts.defaultLongitude) {  this.opts.defaultLongitude = NZLongitude; }
	if(!this.opts.defaultZoom) {  this.opts.defaultZoom = NZZoom; }
	if(!this.opts.defaultTitle) {this.opts.defaultTitle = "Map Ready"; }
	if(!this.opts.customSearchFormName) {this.opts.customSearchFormName + "Around Here";}
	var el = null;
	//clear html areas
	if(this.opts.changePageTitle) {document.title = this.opts.defaultTitle;}
	if(el = document.getElementById(this.opts.titleId)) {el.innerHTML = this.opts.defaultTitle;}
	if(el = document.getElementById(this.opts.layerListId)) {el.innerHTML = "";} else {this.opts.layerListId = "";}
	if(el = document.getElementById(this.opts.sideBarId)) {el.innerHTML = "";} else {this.opts.sideBarId = "";}
	if(el = document.getElementById(this.opts.dropBoxId)) {el.innerHTML = "";} else {this.opts.dropBoxId = "";}
	if(el = document.getElementById(this.opts.statusDivId)) {el.innerHTML = "loading map . . .";} else {this.opts.statusDivId = "statusDivOnMap";}
	if(el = document.getElementById(this.opts.directionsDivId)) {el.innerHTML = "";} else {this.opts.directionsDivId = "";}
	this.clearRouteVariables();
	this.mapAddress = "";
	this.currentlySetToString = "<br />Currently set to: ";
	//setup map
	if(!map) {
		this.setupMap(mapDivName);
	}
	this.zoomTo(this.opts.defaultLatitude, this.opts.defaultLongitude, this.opts.defaultZoom);
	map.clearOverlays();
	if(this.opts.mapTypeDefaultZeroToTwo) {
		mapTypeArray = map.getMapTypes();
		map.setMapType(mapTypeArray[this.opts.mapTypeDefaultZeroToTwo+0]);
	}
	if(this.defaultUrl) {
		this.downloadXml(this.defaultUrl);
	}
	this.mapOriginalSize = map.getSize();
	this.updateStatus("Map Ready");
}
/* map setup and map changes (e.g. zoom) */
GMC.prototype.setupMap = function (mapDivName) {
	map = new GMap2(document.getElementById(mapDivName));
	//standard
	new GKeyboardHandler(map);
	map.enableContinuousZoom();
	//map.enableDoubleClickZoom();
	//optional
	map.addMapType(G_PHYSICAL_MAP);
	if(this.opts.mapAddTypeControl) { map.addControl(new GMapTypeControl()); }
	if(this.opts.mapScaleInfoSizeInPixels > 0) {map.addControl(new GScaleControl(this.opts.mapScaleInfoSizeInPixels)); }
	if(this.opts.mapControlSizeOneToThree == 3) {
		map.addControl(new GLargeMapControl()); }
	else if(this.opts.mapControlSizeOneToThree == 2) {
		map.addControl(new GSmallMapControl());
	}
	else {
		map.addControl(new GSmallZoomControl());
	}
	//add statusDiv
	if(this.opts.statusDivId == "statusDivOnMap" && !this.opts.noStatusAtAll) {
		map.addControl(new this.statusDivControl);
	}
	if(this.opts.addDirections) {
		directions = new GDirections(map);
		GEvent.addListener(directions, "load",  function() {
				GMO.directionsOnLoad();
		});
		GEvent.addListener(directions, "error", this.directionsHandleErrors);
		G_START_ICON.iconSize = new GSize(0, 0);
		G_START_ICON.image="";
		G_START_ICON.shadow = "";
		G_END_ICON.iconSize = new GSize(0, 0);
		G_END_ICON.image="";
		G_END_ICON.shadow = "";
	}
	GEvent.addListener(map, "click", function(marker, point) {
		if(marker) {
				//update marker details
			}
			else {
				GMO.zoomTo(point.lat(), point.lng(), map.getZoom()+1);
			}
		}
	);
	GEvent.addListener(map, "singlerightclick", function(point, image, marker) {
			if(marker.markerName) {
				//GEvent.trigger(marker, "clickHideMe");
				marker.hide();
				map.closeInfoWindow();
				GMO.updateLists();
			}
			else {
				point = map.fromContainerPixelToLatLng(point);
				if(GMO.opts.addPointsToMap) {
					var nameString = "Longitude (" + Math.round(point.lng()*10000)/10000 + ") and Latitude (" + Math.round(point.lat()*10000)/10000 + ")";
					var pointLngLat = point.lng() + "," + point.lat();
					var description = "Manually added point.";
					xmlString = GMO.createPointXml(nameString,pointLngLat,description);
					GMO.processXml(xmlString);
				}
				else {
					GMO.zoomTo(point.lat(), point.lng(), map.getZoom()-1);
				}
			}
		}
	);
	if(this.opts.viewFinderSize > 0) {
		this.viewFinderSize = this.opts.viewFinderSize;
		this.addViewFinder(this.opts.viewFinderSize, this.opts.viewFinderSize);
	}
}
GMC.prototype.basicResetMap = function () {
	map.checkResize();
}
GMC.prototype.addViewFinder = function (width, height) {
	var ovSize = new GSize(width, height);
	var ovMap = new GOverviewMapControl(ovSize);
	map.addControl(ovMap);
	window.setTimeout(
		function() {
			var mini = ovMap.getOverviewMap();
			ovMap.hide(true);
			GEvent.addListener(mini,"load",function(){
				ovMap.hide(true);
			});
		}, 1000
	);
}
GMC.prototype.zoomTo = function (latitude, longitude, zoom) {
	latitude = this.checkLatitude(latitude);
	longitude = this.checkLongitude(longitude);
	zoom = this.checkZoom(zoom);
	if(latitude && longitude && zoom) {
		map.setCenter(new GLatLng(latitude, longitude, true), zoom);
	}
	else if(latitude != 0 && longitude != 0) {
		map.setCenter(new GLatLng(latitude, longitude, true));
	}
}
GMC.prototype.statusDivControl = function () { }
if(mapFunctionIsDefined("GControl")) {
	GMC.prototype.statusDivControl.prototype = new GControl();
}
GMC.prototype.statusDivControl.prototype.initialize = function(map) {
	var el = document.createElement("div");
	el.setAttribute('id',"statusDivOnMap");
	el.style.backgroundColor = "white";
	el.style.font = "small Arial";
	el.style.border = "1px solid black";
	el.style.padding = "2px";
	el.style.zIndex = "99999";
	el.style.marginRight = "7px";
	/*
	GEvent.addDomListener(el, "click", function() {
		el.style.display = "none";
	});
	*/
	var pos = new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(68,19)); //73, 27
	pos.apply(el);
	map.getContainer().appendChild(el);
	return el;
}
/* add and delete layers */
GMC.prototype.changeLayerVisibility = function(selectedLayerId){//remove layer
	var newStatus = 1;
	var newStatusName;
	var count = 0;
	if(this.layerInfo[selectedLayerId].show) {
		newStatus = 0;
		map.closeInfoWindow();
	}
	for (var i = 0; i < this.gmarkers.length; i++) {
		if (this.gmarkers[i].layerId == selectedLayerId) {
			count++;
			if(newStatus) {
				this.gmarkers[i].show();
				newStatusName = "shown";
			}
			else {
				this.gmarkers[i].hide();
				newStatusName = "hidden";
			}
		}
	}
	this.layerInfo[selectedLayerId].show = newStatus;
	this.updateLists();
	this.updateStatus("Points (" + count + ") " + newStatusName + ".");
}
/* add and delete markers / polylines / polygons */
GMC.prototype.createMarker = function(point,name,desc, serverId, iconUrl) {// Create Marker
	var currentLayerId = this.layerInfo.length - 1;
	//marker options
	var markerOpts = this.opts.markerOptions || {};
	if(!iconUrl) {
		iconUrl = this.layerInfo[currentLayerId].iconUrl[0];
	}
	var icon = this.createStandardIcon(iconUrl);
	markerOpts.icon = icon;
	if(markerOpts.title) {
		markerOpts.title = name;
	}
	// create the marker
	var m = new GMarker(point, markerOpts);
	this.updateAddressFormFields(m.getPoint().lat(), m.getPoint().lng());
	//set other marker variables
	m.layerId = currentLayerId;
	m.markerName = name;
	m.markerDesc = desc;
	m.type = 'marker';
	m.serverId = serverId;
	if(this.opts.updateServerUrlDragend) {
		markerOpts.draggable = true;
		GEvent.addListener(m, "dragend", function() {
			GMO.updateStatus('<p>updating database</p>');
			var lng = m.getPoint().lng();
			var lat = m.getPoint().lat();
			jQuery.get(
				GMO.opts.updateServerUrlDragend,
				{ x: lng, y: lat, i: m.serverId },
				function(data){
					GMO.updateStatus('<p>' + data + '</p>');
				}
			);
		});
	}
	else {
		markerOpts.draggable = false;
		m.draggable = false;
	}
	GEvent.addListener(m, "click", function() {
			GMO.lastMarker = m;
			GMO.openMarkerInfoTabs(m);
	});
	map.addOverlay(m);
	this.gmarkers.push(m);
	return m;
}
GMC.prototype.createPolyline = function(points,color,width,opacity,name,desc) {
	var currentLayerId = this.layerInfo.length - 1;
	//marker options
	var markerOpts = this.opts.markerOptions || {};
	//something about the style of the line here
	if(markerOpts.title) {
		markerOpts.title = name;
	}
	// create the marker
	var p = new GPolyline(points,color,width,opacity);
	//set other marker variables
	p.layerId = currentLayerId;
	p.markerName = name;
	p.markerDesc = desc;
	p.type = 'polyline';
	GEvent.addListener(p, "click", function() {
		GMO.lastMarker = p;
		GMO.openPolyInfoTabs(p, false);
	});
	map.addOverlay(p);
	this.gmarkers.push(p);
	return p;
}
GMC.prototype.createPolygon = function(points,color,width,opacity,fillcolor,fillopacity,pbounds, name, desc) {
	var currentLayerId = this.layerInfo.length - 1;
	//marker options
	var markerOpts = this.opts.markerOptions || {};
	//something about the style of the line here
	if(markerOpts.title) {
		markerOpts.title = name;
	}
	// create the marker
	var p = new GPolygon(points,color,width,opacity,fillcolor,fillopacity)
	//set other marker variables
	p.layerId = currentLayerId;
	p.markerName = name;
	p.markerDesc = desc;
	p.type = 'polygon';
	GEvent.addListener(p, "click", function() {
			GMO.lastMarker = p;
			GMO.openPolyInfoTabs(p, pbounds);
	});
	map.addOverlay(p);
	this.gmarkers.push(p);
	return p;
}
GMC.prototype.openMarkerInfoTabs = function(m) {
	var hiddenMarkerArray = GMO.checkForHiddenMarkers(m);
	var name = m.markerName;
	var desc = m.markerDesc;
	var point = m.getPoint();
	var options = this.opts.infoWindowOptions || {};
	//add delete category
	var extraLinks = '';
	var obscuringLinks = '';
	var pointCount = this.layerInfo[m.layerId].pointCount;
	if(pointCount > 1) {
		//extraLinks += ', <a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'hideGroup\');">Hide Group ('+ pointCount +' points)</a>';
		GEvent.addListener(m, "hideGroup", function() {
				GMO.changeLayerVisibility(m.layerId);
				map.closeInfoWindow();
		});
	}
	if(this.opts.addAntipodean) {
		extraLinks += ', <a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickAntipodean\');">drill a hole</a>';
		GEvent.addListener(m, "clickAntipodean", function() {
			map.setZoom(6);
			var point = GMO.antipodeanPointer(m.getPoint().lat(), m.getPoint().lng());
			var longitude = GMO.checkLongitude(point.lng());
			var latitude = GMO.checkLatitude(point.lat());
			var nameString = "Antipodean of " + m.markerName;
			var pointLngLat = longitude+","+latitude;
			var description = "Exact opposite point on earth - goodluck finding your way back...";
			var zoom = 3;
			GMO.processXml(GMO.createPointXml(nameString, pointLngLat, description, latitude, longitude, zoom));
		});
	}
	var moveNotice = '';
	if(m.draggable()) {
		moveNotice = ' - <b>marker can be dragged to new location once this info window has been closed</b>';
	}
	if(hiddenMarkerArray.length) {
		obscuringLinks += ' <p><span class="partialObscuring">This marker (partially) obscures the following point(s): ';
		for(var i = 0; i < hiddenMarkerArray.length; i++) {
			var markerId = hiddenMarkerArray[i];
			obscuringLinks += ' <a href="javascript:void(0)" onclick="GMO.showMarkerFromList('+markerId+');">' + this.gmarkers[markerId].markerName + '</a>';
			if(i < (hiddenMarkerArray.length - 2)) {
				obscuringLinks += ", ";
			}
			else if(i == (hiddenMarkerArray.length - 2 )){
				obscuringLinks += " and ";
			}
		}
		obscuringLinks += "</span></p>";
	}
	//basic html
	var html = '<div id="infoWindowTab1" class="infoWindowTab">' + obscuringLinks + '<h1>'+name+'</h1><div>'+desc+'</div>'
		+ '<p class="infoTabExtraLinks">'
		+ '<a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickZoomIn\')">Zoom In</a>, '
		+ '<a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickCloseUp\')">Close-Up</a> '
		//+ '<a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickHideMe\')">Delete Marker</a>, '
		//+ '<a href="javascript:void(0)" onclick="map.closeInfoWindow();">Close Window</a>'
		+ extraLinks
		+ moveNotice
		+ '.</p>';
	GEvent.addListener(m, "clickZoomIn", function() {
		GMO.zoomTo(m.getPoint().lat(), m.getPoint().lng(), map.getZoom() + 2);
	});
	GEvent.addListener(m, "clickCloseUp", function() {
		m.showMapBlowup();//zoom into marker
	});
	GEvent.addListener(m, "clickHideMe", function() {
		map.closeInfoWindow();
		m.hide();
		var currentLayerId = m.layerId
		GMO.updateLists();
	});
	var tabsHtml = [new GInfoWindowTab("info", html)];
	//directions and address finder
	if(this.opts.addDirections) {
		var lonLatString = point.toUrlValue();
		var findDirections = '';
		if(this.opts.addDirections) {
			findDirections = ''
				+ '<p class="infoTabFromOption"><b>From:</b></p><p id="fromHereLink"><a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickFromHere\')">select this point</a>' + this.currentFromLocation() + '</p>'
				+ '<p class="infoTabToOption"><b>To:</b></p><p id="toHereLink"><a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickToHere\')">select this point</a>' + this.currentToLocation() + '</p>'
				+ '<p class="infoTabAlternative">';
			if(this.routeShown) {
				findDirections += ''
					+ '<br /><br /><b>Do next:</b></p><p><a href="javascript:GEvent.trigger(GMO.lastMarker,\'clickClearRoute\')" id="clearRouteLink">Clear Last Route</a> ';
			}
			else if((this.floatFrom || this.floatTo) || (this.to || this.from)) {
				findDirections += ''
					+ '<br /><br /><b>Do next:</b></p><p><a href="javascript:void(0)" class="submitButton" onclick="GEvent.trigger(GMO.lastMarker,\'clickFindRoute\')" id="calculateLinkRoute">Calculate Route</a>';
			}
			else if(this.layerInfo[m.layerId].pointCount > 1 && this.layerInfo[m.layerId].pointCount < 20) {
				findDirections += ''
					+ '<br /><br /><b>Alternatively:</b></p><p><a href="javascript:void(0)" class="submitButton" onclick="GEvent.trigger(GMO.lastMarker,\'joinTheDots\')" id="calculateLinkRoute">Create Route Using Current Points</a>';
			}
			findDirections += ''
				+ '</p>'
			//join the dots
			GEvent.addListener(m, "joinTheDots", function() {
				GMO.createRouteFromLayer(m.layerId);
				map.closeInfoWindow();
			});
			//current start point
			GEvent.addListener(m, "clickFromHere", function() {
				GMO.from = name;
				GMO.floatFrom = lonLatString;
				document.getElementById("fromHereLink").innerHTML = "This point";
			});
			//current end point
			GEvent.addListener(m, "clickToHere", function() {
				document.getElementById("toHereLink").innerHTML = "This point";
				GMO.to = name;
				GMO.floatTo = lonLatString;
			});
			//add route
			GEvent.addListener(m, "clickFindRoute", function() {
				GMO.showRoute();
				map.closeInfoWindow();
			});
			//clearRoute
			GEvent.addListener(m, "clickClearRoute", function() {
				GMO.clearRouteAll();
			});
		}
		tabsHtml.push(new GInfoWindowTab("directions", '<div id="infoWindowTab2" class="infoWindowTab">' + findDirections + '</div>' ));
	}
	if(this.opts.addCustomSearchForm) {
		tabsHtml.push(new GInfoWindowTab(this.opts.customSearchFormName, '<div id="infoWindowTab3" class="infoWindowTab">' + this.opts.customSearchFormHtml + '</div>'));
	}
	m.openInfoWindowTabsHtml(tabsHtml,options);
}

GMC.prototype.openPolyInfoTabs = function( m, pbounds) {
	//if pbounds then it must be a polygon rather than a polyline
	var name = m.markerName;
	var desc = m.markerDesc;
	var options = this.opts.infoWindowOptions || {};
	//add delete category
	var extraLinks = '';
	var pointCount = this.layerInfo[m.layerId].pointCount;
	if(pointCount > 1) {
		//extraLinks += ', <a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'hideGroup\');">Hide Group ('+ pointCount +' points)</a>';
		GEvent.addListener(m, "hideGroup", function() {
				GMO.changeLayerVisibility(m.layerId);
				map.closeInfoWindow();
		});
	}
	//basic html
	var html = '<div id="infoWindowTab1" class="infoWindowTab"><h1>'+name+'</h1><div>'+desc+'</div>'
		+ '<p class="infoTabBasicLinks">'
		+ '<a href="javascript:void(0)" onclick="map.closeInfoWindow();">Close Window</a>'
		+ ', <a href="javascript:void(0)" onclick="GEvent.trigger(GMO.lastMarker,\'clickHideMe\')">Remove Item</a>'
		+ extraLinks
		+ '.</p>'
	GEvent.addListener(m, "clickHideMe", function() {
		map.closeInfoWindow();
		m.hide();
		var currentLayerId = m.layerId
		GMO.updateLists();
	});
	var tabsHtml = [new GInfoWindowTab("info", html)];
	//directions and address finder
	if(pbounds) {
		map.openInfoWindowHtml(pbounds.getCenter(),html,options);
	}
	else {
		map.openInfoWindowHtml(m.getVertex(Math.floor(m.getVertexCount()/2)),html,options);
	}
}


GMC.prototype.preLoadMarkerImages = function(desc) {
	var text = desc;
	var pattern = /<\s*img/ig;
	var result;
	var pattern2 = /src\s*=\s*[\'\"]/;
	var pattern3 = /[\'\"]/;
	while ((result = pattern.exec(text)) != null) {
		var stuff = text.substr(result.index);
		var result2 = pattern2.exec(stuff);
		if (result2 != null) {
			stuff = stuff.substr(result2.index+result2[0].length);
			var result3 = pattern3.exec(stuff);
			if (result3 != null) {
				var imageUrl = stuff.substr(0,result3.index);
				this.markerImageCollection[this.imageNum] = new Image();
				this.markerImageCollection[this.imageNum].src = imageUrl;
				this.imageNum++;
			}
		}
	}
}
GMC.prototype.createStandardIcon = function(iconUrl) {
	// create icon
	var icon = new GIcon(G_DEFAULT_ICON);
	this.updateStatus(iconUrl);
	icon.image = iconUrl;
	icon.iconSize = new GSize(this.opts.iconWidth,this.opts.iconHeight);
	icon.shadowSize = new GSize(0,0);
	icon.shadow = '';
	icon.dragCrossAnchor = new GPoint(this.opts.iconWidth/2,this.opts.iconWidth/2);
	icon.dragCrossSize = new GSize(this.opts.iconWidth,this.opts.iconWidth);
	icon.iconAnchor = new GPoint(Math.round(this.opts.iconWidth/2),this.opts.iconHeight);
	icon.infoWindowAnchor = new GPoint(Math.round(this.opts.iconWidth/2),Math.round(this.opts.iconHeight/2));
	return icon;
}
/* process XML sheets */
GMC.prototype.createPointXml = function (name, pointLngLat, description, latitude, longitude, zoom, info) {//creates XML for one point only
	//change first three to arrays
	var serverId = "Marker_manuallyAdded" + GMO.layerInfo.length;
	var string = '<?xml version="1.0" encoding="UTF-8"?>'
	+ '<kml xmlns="http://earth.google.com/kml/2.1"><Document>'
	+ '<title>' + name + '</title>'
	+ '<longitude>' + longitude + '</longitude>'
	+ '<latitude>' + latitude + '</latitude>'
	+ '<zoom>' + zoom + '</zoom>'
	+ '<pointcount>1</pointcount>'
	+ '<info>' + info + '</info>'
	+ '<Placemark>'
	+ ' <id>' + serverId + '</id>'
	+ ' <name>' + name + '</name>'
	+ ' <Point><coordinates>' + pointLngLat + '</coordinates></Point>'
	+ ' <description>' + description +'</description>'
	+ '</Placemark>'
	+ '</Document>'
	+'</kml>';
	return string;
}

GMC.prototype.downloadXml = function(url) {
	var previouslyLoaded;
	if(previouslyLoaded = this.layerInfo.inSubArray("url", url)) {
		this.updateStatus("Already loaded");
		var realLayerId = previouslyLoaded - 1;
		if(!this.layerInfo[realLayerId].show) {
			this.changeLayerVisibility(previouslyLoaded-1);
		}
	}
	else {
		this.updateStatus("Downloading data from server . . . ");
		this.latestUrl = url;
		GDownloadUrl(url, function(doc) { GMO.processXml(doc);} );
	}
}
GMC.prototype.processXml = function(doc) {
	var xmlDoc = GXml.parse(doc);
	this.bounds = new GLatLngBounds();
	var pointCount = GXml.value(xmlDoc.getElementsByTagName("pointcount")[0]);
	this.tooManyPointsWarning(pointCount);
	if(pointCount > 0) {
		var currentLayerId = this.layerInfo.length;
		var groupInfo = {};
		iconUrlCollection = [];
		groupInfo.show = 1;
		groupInfo.url = this.latestUrl;
		this.latestUrl = '';
		groupInfo.pointCount = pointCount;
		//parse basics:
		groupInfo.title =  GXml.value(xmlDoc.getElementsByTagName("title")[0]);
		groupInfo.info = GXml.value(xmlDoc.getElementsByTagName("info")[0]);
		groupInfo.a = GXml.value(xmlDoc.getElementsByTagName("latitude")[0]);
		groupInfo.o = GXml.value(xmlDoc.getElementsByTagName("longitude")[0]);
		groupInfo.z = GXml.value(xmlDoc.getElementsByTagName("zoom")[0]);
		//add icon here? createStandardIcon
		var currentIconId = currentLayerId + 1;
		var layerIconCount = 0;
		if(currentIconId > this.opts.iconMaxCount) {
			currentIconId = 1;
		}
		if(currentIconId && (this.opts.iconFolder || this.opts.defaultIconUrl)) {
			var iconUrl = this.opts.defaultIconUrl || this.opts.iconFolder + "i" + currentIconId + "." + this.opts.iconExtension;
		}
		//add layer information ... IMPORTANT MUST BE BEFORE MARKER LOOP
		//groupInfo.iconUrl = iconUrlCollection;
		this.layerInfo.push (groupInfo);
		// Read through the Placemarks
		var placemarks = xmlDoc.documentElement.getElementsByTagName("Placemark");
		for (var i = 0; i < placemarks.length; i++) {
			var serverId = GXml.value(placemarks[i].getElementsByTagName("id")[0]);
			var name = GXml.value(placemarks[i].getElementsByTagName("name")[0]);
			var desc = GXml.value(placemarks[i].getElementsByTagName("description")[0]);
			var styleLocationId = GXml.value(placemarks[i].getElementsByTagName("styleUrl")[0]);
			styleLocationId = styleLocationId.substring(1, styleLocationId.length);
			var newIconURL = iconUrl;
			if(styleLocationId) {
				//<Style id="randomColorIcon"><IconStyle><Icon>URL here
				var IconStyleDoc = xmlDoc.getElementsByTagName("Style");
				for(var j=0;j<IconStyleDoc.length;j++){
					if(IconStyleDoc[j].getAttribute("id")) {
						if(IconStyleDoc[j].getAttribute("id") == styleLocationId){
							layerIconCount++;
							newIconURL = GXml.value(IconStyleDoc[j].getElementsByTagName("Icon")[0]);
						}
					}
				}
			}
			// Attempt to preload images
			if (this.opts.preloadImages) {
				this.preLoadMarkerImages(desc);
			}
			var coords = GXml.value(placemarks[i].getElementsByTagName("coordinates")[0]);
			coords=coords.replace(/\s+/g," "); // tidy the whitespace
			coords=coords.replace(/^ /,"");    // remove possible leading whitespace
			coords=coords.replace(/, /,",");   // tidy the commas
			var path = coords.split(" ");
			// Is this a polyline/polygon?
			if (path.length > 1) {
				// Build the list of points
				var points = [];
				var pbounds = new GLatLngBounds();
				for (var p=0; p<path.length-1; p++) {
					var bits = path[p].split(",");
					var point = new GLatLng(parseFloat(bits[1]),parseFloat(bits[0]));
					points.push(point);
					this.bounds.extend(point);
					pbounds.extend(point);
				}
				if(this.opts.polyIcon) {
					newIconURL = this.opts.polyIcon;
				}
				var width = this.opts.lineWidth;
				var color = this.opts.lineColour;
				var opacity = this.opts.lineOpacity;
				var linestring = placemarks[i].getElementsByTagName("LineString");
				var polygons = placemarks[i].getElementsByTagName("Polygon");
				if (linestring.length) {
					var p = this.createPolyline(points,color,width,opacity,name,desc);
				}
				else if (polygons.length) {
					var fillopacity = this.opts.fillOpacity;
					var fillcolor = this.opts.fillColour;
					var p = this.createPolygon(points,color,width,opacity,fillcolor,fillopacity,pbounds, name, desc);
				}
				else {
					alert("sorry - could not load data");
				}
			}
			else {
			//it must be a marker
				var bits = path[0].split(",");
				var point = new GLatLng(parseFloat(bits[1]),parseFloat(bits[0]));
				this.bounds.extend(point);
			// create marker
				this.updateStatus("Processing new point " + i + " of " + pointCount + " . . .");
				var m = this.createMarker(point, name, desc, serverId, newIconURL);
			}
			if(!iconUrlCollection.inArray(newIconURL)) {
				iconUrlCollection.push(newIconURL);
			}
		}
		//add icons
		var currentLayerId = this.layerInfo.length - 1;
		this.layerInfo[currentLayerId].iconUrl = iconUrlCollection;
		// Shall we zoom to the bounds?
		if (pointCount > 1) {
			//map.setZoom(map.getBoundsZoomLevel(that.bounds));
			this.zoomTo(this.bounds.getCenter().lat(), this.bounds.getCenter().lng(), map.getBoundsZoomLevel(this.bounds));
			//map.setCenter(this.bounds.getCenter());
		}
		else {
			map.panTo(point);
		}
		this.updateLists();
		this.zoomTo(groupInfo.a, groupInfo.o, groupInfo.z);
		if(!map.getInfoWindow().isHidden()) {
			map.closeInfoWindow();
		}
		if(pointCount > 1) {
			this.updateStatus(pointCount + " points added.");
		}
		else {
			window.setTimeout(
				function () {
					GEvent.trigger(GMO.gmarkers[GMO.gmarkers.length -1], "click");
					map.panDirection(0, 1);
				}
				, 300
			);
			this.updateStatus("One point loaded.");
		}
	}
	else {
		var title =  GXml.value(xmlDoc.getElementsByTagName("title")[0]);
		if(el = document.getElementById(this.opts.titleId)) {
			el.innerHTML = title;
		}
		if(this.opts.changePageTitle && document.title) {
			document.title = title;
		}
	}
}

GMC.prototype.tooManyPointsWarning = function(pointCount) {
	if(pointCount > 100) {
		this.updateStatus("In total, " + pointCount + " points are being loaded. It may take a while for all the points to show on the map, please be patient.");
	}
	else if(pointCount == 0) {
		this.updateStatus("No points could be found.");
	}
}

/* update lists */
GMC.prototype.updateLists = function() {
	this.updateStatus("Updating lists . . .");
	if(this.opts.sideBarId || this.opts.dropBoxId || this.opts.layerListId) {
		var a = [];
		var sideBarArray = [];
		for(var i = 0; i < this.layerInfo.length; i++) {
			this.layerInfo[i].show = 0;
		}
		for (var i = 0; i < this.gmarkers.length; i++) {
			if (!this.gmarkers[i].isHidden()) {
				this.layerInfo[this.gmarkers[i].layerId].show = 1;
				if(!a.inArray(this.gmarkers[i].markerName)) {
					sideBarArray.push(this.gmarkers[i].markerName + "$$$" + i);
				}
				a.push(this.gmarkers[i].markerName);
			}
		}
		sideBarArray.sort();
	}
	if(this.opts.sideBarId) {
		this.createSideBar(sideBarArray);
	}
	if(this.opts.dropBoxId) {
		this.createDropDown(sideBarArray);
	}
	if(this.opts.titleId) {
		this.updateTitles();
	}
	if(this.opts.layerListId) {
		this.updateLayerList();
	}
	this.updateStatus("Map Ready");
}
GMC.prototype.createSideBar = function(sideBarArray) {
	var el;
	if(el = document.getElementById(this.opts.sideBarId)) {
		var html = '<ul id="' + this.opts.sideBarId + 'list">';
		for (var j = 0; j < sideBarArray.length; j++) {
			var sideBarElements = sideBarArray[j].split("$$$", 2);
			i = sideBarElements[1];
			if(!strpos(this.gmarkers[i].serverId, "manuallyAdded", 0)) {
				html += '<li><a href="'+ this.currentPageURL + '#map" onclick="GMO.showMarkerFromList(' + i + ');">' + this.gmarkers[i].markerName + '</a> <span class="infowindowDetails">'  + this.gmarkers[i].markerDesc + '</span></li>';
			}
			else {
				html += '<li>You added: <a href="'+ this.currentPageURL + '#map" onclick="GMO.showMarkerFromList(' + i + ');">' + this.gmarkers[i].markerName + '</a></li>';
			}
		}
		html += '</ul>';
		el.innerHTML = html;
	}
}
GMC.prototype.createDropDown = function(sideBarArray) {
	var el;
	if(el = document.getElementById(this.opts.dropBoxId)) {
		var html = '<select onchange="GMO.showMarkerFromList(this.value);"><option selected="selected"> - Select a location - </option>';
		for (var j = 0; j < sideBarArray.length; j++) {
			var sideBarElements = sideBarArray[j].split("$$$", 2);
			i = sideBarElements[1];
			html += '<option value="' + i + '">' + this.gmarkers[i].markerName +'</option>';
		}
		html + "</select>";
		el.innerHTML = html;
	}
}
GMC.prototype.updateLayerList = function() {
	var el = null;
	if(el = document.getElementById(this.opts.layerListId)) {
		var html = '<ul>';
		var className = '';
		var style = '';
		var linkText = '';
		for (var i = 0; i < this.layerInfo.length; i++) {
			if (this.layerInfo[i].show == 1) {
				linkText = "hide";
				className = "layerListShown";
				style = '';
			}
			else {
				linkText = "show";
				className = "layerListHidden";
				if(this.opts.hiddenLayersRemovedFromList) {
					style = ' style="display: none;"';
				}
				else {
					style = ' style="opacity:.30; filter: alpha(opacity=30); -moz-opacity: 0.30;"';
				}
			}
			html += ''
				+ '<li class="'+className+'"'+style+'>';
			for(var j=0; j < this.layerInfo[i].iconUrl.length; j++) {
				html += ''
					+ '<img src="'+ this.layerInfo[i].iconUrl[j] + '" alt="icon ' + j + ' for '+ this.layerInfo[i].title + '" class="iconBulletPoint" width="' + this.opts.iconWidth + '" height="' + this.opts.iconHeight + '" /> ';
			}
			html += ''
				+ this.layerInfo[i].title
				+ ' - <a href="javascript:void(0)" onclick="GMO.changeLayerVisibility('+i+')">' + linkText + '</a>';
				if(!this.opts.hiddenLayersRemovedFromList) {
					+ ' - <a href="javascript:void(0)" onclick="GMO.deleteLayer('+i+')">delete</a>';
				}
			if(this.opts.addKmlLink && this.layerInfo[i].url) {
				html += ''
					+ ' (<a href="' + this.layerInfo[i].url + '&kml=1">kml</a>)</li>';
			}
		}
		html += "<ul>";
		el.innerHTML = html;
	}
}
GMC.prototype.updateTitles = function() {
	var title = this.opts.defaultTitle;
	for (var i = (this.layerInfo.length - 1); i > -1; i--) {
		if(this.layerInfo[i].show) {
			title = this.layerInfo[i].title;
			i = -99;
		}
	}
	var el = null
	if(el = document.getElementById(this.opts.titleId)) {
		el.innerHTML = title;
	}
	if(this.opts.changePageTitle && document.title) {
		document.title = title;
	}
}


GMC.prototype.updateStatus = function(html, add) {
	var el = null;
	/* depreciated...
	if(this.opts.addAddressFinder || add == "find") {
		if(html) {
			html += "<hr />";
		}
		html += this.findAddressForm() + "";
	}
	*/
	if(add == "help") {
		if(html) {
			html += "<hr />";
		}
		html += "<hr />" + this.helpHtml() + "";
	}
	else {
		//do nothing
	}
	if(document.getElementById(this.mapDivName)) {
		if(this.mapIsZoomed) {
			var zoomLinkLabel = 'reduce map size';
			var hideAction = ""
		}
		else {
			var zoomLinkLabel = 'full-screen';
			var hideAction = '| <a href="javascript:void(0)" onclick="GMO.hideStatus();">hide [x]</a>';
		}
	}
	var fullHtml = ''
		+ '<p class="helpLink" style="text-align: right; font-size: 10px; width: auto; float: right;">';
		/* depreciated
		if(this.opts.addAddressFinder) {
			fullHtml += ' <a href="javascript:void(0)" onclick="GMO.updateStatus(\'\', \'find\');">find address</a> |'
		}
		*/
		fullHtml += ' <a href="javascript:void(0)" onclick="GMO.updateStatus(\'\', \'help\');">show help</a> |'
		+ ' <a href="javascript:void(0)" onclick="GMO.enlargeMap();" id="mapZoomLinkLabel">' + zoomLinkLabel + '</a> '
		+ hideAction
		+ '</p>'+ html;
	if(el = document.getElementById(this.opts.statusDivId)) {
		el.innerHTML = fullHtml;
		el.style.display = "block";
	}
}
GMC.prototype.hideStatus = function() {
	if(el = document.getElementById(this.opts.statusDivId)  && !this.mapIsZoomed) {
		el = document.getElementById(this.opts.statusDivId);
		el.style.display = "none";
	}
	else if(this.mapIsZoomed) {
		alert("Can not hide this bar in full screen mode");
	}
}

/* special searches: find address */
GMC.prototype.showAddress = function(address) {
	geocoder = new GClientGeocoder();
	if(this.opts.defaultCountryCode) {
		geocoder.setBaseCountryCode(this.opts.defaultCountryCode);
	}
	if (geocoder) {
		this.updateStatus("Searching for Address . . .");
		this.mapAddress = address;
		if(this.opts.defaultAddressText) {
			address += this.opts.defaultAddressText;
		}
		geocoder.setViewport(geocoder.getViewport());
		geocoder.getLocations(address, this.addAddressToMap);
		return false;
	}
	else {
		this.updateStatus("Address Finder Not Loaded");
	}
}
GMC.prototype.addAddressToMap = function (response) {
	if (!response || response.Status.code != 200) {
		GMO.updateStatus("Sorry, we were unable to find that address.");
	}
	else {
		place = response.Placemark[0];
		var pointLngLat = place.Point.coordinates[0] + "," + place.Point.coordinates[1];
		var nameString = "Address Search: " + GMO.mapAddress;
		var description = place.address;
		GMO.processXml(GMO.createPointXml(nameString, pointLngLat, description));
		GMO.updateStatus("Address found: " + description);
		var serverURL = GMO.opts.updateServerUrlAddPoint+"&x=" + place.Point.coordinates[0] + "&y=" + place.Point.coordinates[1]
		addLayer(serverURL);
		return true;
	}
}
GMC.prototype.updateAddressFormFields = function(latitude, longitude) {
	if(el = document.getElementById(GMO.opts.latFormFieldId)) {
		document.getElementById(GMO.opts.latFormFieldId).value = latitude;
	}
	if(document.getElementById(GMO.opts.lngFormFieldId)) {
		document.getElementById(GMO.opts.lngFormFieldId).value = longitude;
	}
}
/* special searches: find route */
GMC.prototype.showRoute = function() {
	if(!this.floatFrom) {
		this.updateStatus("No valid starting point has been selected.");
	}
	else if(!this.floatTo) {
		this.updateStatus("No valid end point has been selected.");
	}
	else {
		GMO.routeShown = true;
		this.updateStatus("Searching for Route . . .");
		var fromTo = this.floatFrom + " to " + this.floatTo;
		var directionOptions = { locale: this.opts.localeForResults, getSteps:true}
		directions.load(fromTo, directionOptions);
	}
}

GMC.prototype.createRouteFromLayer = function(layerId) {
	var wayPointArray = Array();
	var k = 0;
	var doAllMarkers = false;
	var pointCount = this.layerInfo[layerId].pointCount;
	if(pointCount < 2) {
		var doAllMarkers = true;
	}
	for (var i = 0; i < this.gmarkers.length; i++) {
		if(this.gmarkers[i].layerId == layerId || doAllMarkers) {
			wayPointArray[k] = (k+1) + ": " + this.gmarkers[i].markerName + "@" + this.gmarkers[i].getPoint().lat() + "," + this.gmarkers[i].getPoint().lng();
			k++;
		}
	}
	if(wayPointArray.length) {
		GMO.routeShown = true;
		this.updateStatus("Searching for Route . . .");
		var directionOptions = { locale: this.opts.localeForResults, getSteps: true};
		directions.loadFromWaypoints(wayPointArray, directionOptions);
	}
	else {
		this.updateStatus("No Route Could Be Found . . .");
	}
}
GMC.prototype.currentFromLocation = function() {
	var string = '';
	if(this.from ) {
		string =  '<span id="currentlySetToFrom">' + this.currentlySetToString + this.from + '</span>';
	}
	else if(this.floatFrom) {
		string = '<span id="currentlySetToTo">' + this.currentlySetToString + this.floatFrom + '</span>';
	}
	return string;
}
GMC.prototype.currentToLocation = function() {
	var string = '';
	if(this.to ) {
		string =  this.currentlySetToString + this.to;
	}
	else if(this.floatTo) {
		string = this.currentlySetToString + this.floatTo;
	}
	return string;
}
GMC.prototype.clearRouteVariables = function () {
	this.to = '';
	this.floatTo = 0;
	this.from = '';
	this.floatFrom = 0;
	var el = null;
	if(el = document.getElementById("currentlySetToFrom")) {el.innerHTML = '';}
	if(el = document.getElementById("currentlySetToTo")) {el.innerHTML = '';}
}
GMC.prototype.clearRouteAll = function () {
	var el = null;
	if(el = document.getElementById(GMO.opts.directionsDivId)) {el.innerHTML = '';}
	this.clearRouteVariables();
	this.routeShown = false;
	directions.clear();
	GMO.updateStatus("Route Cleared");
}
GMC.prototype.directionsHandleErrors = function (){
	if (directions.getStatus().code == G_GEO_UNKNOWN_ADDRESS) GMO.updateStatus("No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.\nError code: " + directions.getStatus().code);
	else if (directions.getStatus().code == G_GEO_SERVER_ERROR) GMO.updateStatus("A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.\n Error code: " + directions.getStatus().code);
	else if (directions.getStatus().code == G_GEO_MISSING_QUERY) GMO.updateStatus("The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.\n Error code: " + directions.getStatus().code);
	//else if (directions.getStatus().code == G_UNAVAILABLE_ADDRESS)  GMO.updateStatus("The geocode for the given address or the route for the given directions query cannot be returned due to legal or contractual reasons.\n Error code: " + directions.getStatus().code);
	else if (directions.getStatus().code == G_GEO_BAD_KEY) GMO.updateStatus("The given key is either invalid or does not match the domain for which it was given. \n Error code: " + directions.getStatus().code);
	else if (directions.getStatus().code == G_GEO_BAD_REQUEST) GMO.updateStatus("A directions request could not be successfully parsed.\n Error code: " + directions.getStatus().code);
	else GMO.updateStatus("An unknown error occurred - Maybe the start or end address could not be found?");
}
GMC.prototype.directionsOnLoad = function (){
	GMO.updateStatus("Route Found - Details Loading . . .");
	var html = '';
	function waypoint(point, type, address, pointName) {
		html += ''
			+ '<tr>'
			+' <th colspan="2">'
			+'  <a href="javascript:void(0);" onclick="map.showMapBlowup(new GLatLng('+point.toUrlValue(6)+')); return false;">'
			+'   <img src="http://www.google.com/intl/en_ALL/mapfiles/icon-dd-' +type+ '-trans.png" alt="marker">'
			+'  </a>'
			+   pointName + " (" + address + ")"
			+' </th>'
			+'</tr>';
	}
	function routeDistance(dist) {
		html +=  ''
			+ '<tr><th colspan="2" class="routeDistance">' + dist + '</th></tr>';
	}
	function copyright(text) {
		html +=  ''
			+ '<tr><th colspan="2" class="copyright">' + text + '</th></tr>';
	}
	function detail(point, num, description, dist) {
		html += ''
			+ '<tr>'
			+' <td>'
			+'  <a href="javascript:void(0);" onclick="map.showMapBlowup(new GLatLng('+point.toUrlValue(6)+'));">'+num+'.</a> '
			+   description
			+' </td>'
			+' <td class="dist">'
			+   dist
			+' </td>'
			+'</tr>';
	}
	function formatDistance(meters) {
		var distanceString = '';
		if (meters > 99) {
			distanceString += ( (meters/1000).toFixed(1) +"&nbsp;km." );
		}
		else {
			distanceString += ( meters +"&nbsp;me." );
		}
		miles = meters * 0.000621371192
		distanceString += " / " + ((miles).toFixed(1) +" mi.");
		return distanceString;
	}
		// === read through the GRoutes and GSteps ===
	for (var i=0; i<directions.getNumRoutes(); i++) {
		if (i==0) {
			var type="play";
			var pointName = GMO.from;
		}
		else {
			var type="pause";
			var pointName = GMO.via;
		}
		var route = directions.getRoute(i);
		var geocode = route.getStartGeocode();
		var point = route.getStep(0).getLatLng();
		// create Html
		waypoint(point, type, geocode.address, pointName);
		routeDistance(formatDistance(route.getDistance().meters)+" - about "+route.getDuration().html);
		for (var j=0; j<route.getNumSteps(); j++) {
			var step = route.getStep(j);
			// === detail lines for each step ===
			detail(step.getLatLng(), j+1, step.getDescriptionHtml(), formatDistance(step.getDistance().meters));
		}
	}
	// === the final destination waypoint ===
	var geocode = route.getEndGeocode();
	var point = route.getEndLatLng();
	var pointName = GMO.to;
	waypoint(point, "stop", geocode.address, pointName);
	copyright(directions.getCopyrightsHtml());
	//open window
	var html =  ''
	+ '<p id="directionsPrintOptions"><a href="javascript:void(0)" onclick="GMO.printDirections()">Print Directions</a>, <a href="javascript:void(0)" onclick="GMO.clearRouteAll();">Clear Route</a></p>'
	+ '<div id="directionsInnerDiv">'
	+ ' <table class="directionsClassTable"><col class="col1" /><col class="col2"/>' + html + '</table>'
	+ '</div>';
	var directionsPanel = document.getElementById(GMO.opts.directionsDivId);
	if(directionsPanel) {
		directionsPanel.innerHTML = html;
		GMO.updateStatus('Directions Loaded - <a href="javascript:void(0)" onclick="GMO.printDirections()">Print Directions</a>.');
	}
	else {
		GMO.openDirectionsPopup(html);
	}
	//clear variables
	GMO.clearRouteVariables();
	GMO.attachStyleDirections(document);
	//setTimeout(GMO.printDirections, 1000);
}
GMC.prototype.enlargeMap = function() {
	var el = document.getElementById(this.mapDivName);
	var elLabel = document.getElementById("mapZoomLinkLabel");
	if(el && elLabel) {
		if(this.mapIsZoomed == true) {
			el.style.width = this.mapOriginalSize.width + "px";
			el.style.height = this.mapOriginalSize.height + "px";
			el.style.position = "relative";
			el.style.top = "";
			el.style.left = "";
			el.style.zIndex = "";
			document.body.style.overflow = 'auto';
			this.mapIsZoomed = false;
			elLabel.innerHTML = "full-screen";
		}
		else {
			el.style.width = this.viewPortWidth()+"px";
			el.style.height = this.viewPortHeight()+"px";
			el.style.position = "fixed";
			el.style.top = 0;
			el.style.left = 0;
			el.style.zIndex = "9";
			document.body.style.overflow = 'hidden';
			this.mapIsZoomed = true;
			elLabel.innerHTML = "original size";
		}
		map.checkResize();
		window.scrollTo(0, 0);
	}
}
GMC.prototype.printDirections = function() {
	var el = null;
	if(el = document.getElementById("directionsInnerDiv")) {
		var html = el.innerHTML
		GMO.openDirectionsPopup(html);
	}
	else {
		GMO.updateStatus("Could not find directions.");
	}
}
GMC.prototype.attachStyleDirections = function(selectedDocument) {
	var styleSheetUrl = GMO.opts.styleSheetUrl;
	if(selectedDocument.createStyleSheet) {
		selectedDocument.createStyleSheet(styleSheetUrl);
	}
	else {
		var styles = "@import url(" + styleSheetUrl + ");"; // NOT USED AT PRESENT
		var newSS = document.createElement('link');
		newSS.rel = 'stylesheet';
		newSS.href = styleSheetUrl;
		selectedDocument.getElementsByTagName("head")[0].appendChild(newSS);
	}
}
GMC.prototype.openDirectionsPopup = function (innerHTML) {
	if(innerHTML) {
		var html = ''
			+ '<html><head><title>Directions</title>'
			+ '<style type="text/css">'
			+ '</style>'
			+ '<body onLoad="self.focus()">'
			+ '<p><a href="javascript:void(0)" onclick="self.print()">Print This page</a>, <a href="javascript:void(0)" onclick="self.close()">Close Window</a></p>'
			+ innerHTML
			+ '</body></html>';
	}
	else {
		GMO.updateStatus("Could not find directions.");
	}
	var url = "";
	var win = null;
	var features = 'width=500,height=500,menubar=1,resizable=1,scrollbars=1';
	var name = "directions";
	win = window.open(url, name, features);
	var newdocument = null;
	if(newdocument = win.document) {
		newdocument.open();
		newdocument.write(html);
		GMO.attachStyleDirections(newdocument);
		newdocument.close();
	}
	else {
		alert("Could not open directions in new window, please try again.");
	}
}
/* marker interaction and computations */
GMC.prototype.getlastMarkerLonLat = function() {
	return this.lastMarker.getPoint();
}
GMC.prototype.showMarkerFromList = function (selectedId) {
	if(selectedId > -1){
		GEvent.trigger(this.gmarkers[selectedId],'click');
	}
}
GMC.prototype.antipodeanPointer = function (lat, lng) {
	var point = {}
	if(lng > 0) {
		lng = -180 + lng;
	}
	else {
		lng = 180 + lng;
	}
	lat = lat * -1;
	this.updateStatus("Hole Drilled!");
	point = new GLatLng(lat, lng, true)
	return point;
}
GMC.prototype.checkLatitude = function(latitude) {
	if(latitude && latitude != 0 && latitude >= -90 && latitude <= 90) {return latitude;} else {return false;}
}
GMC.prototype.checkLongitude = function(longitude) {
	if(longitude && longitude != 0 && longitude >= -180 && longitude <= 180) {return longitude;} else {return false;}
}
GMC.prototype.checkZoom = function(zoom) {
	if(zoom > 0 && zoom < 50) {return zoom;} else {return 0;}
}
/* marker distances */
GMC.prototype.distancePerPixel = function (latLngPoint) {
	var pixelCoordinates = map.fromLatLngToDivPixel(latLngPoint);
	var newPoint = new GPoint(pixelCoordinates.x + 10, pixelCoordinates.y);
	var newlatLngPoint = map.fromDivPixelToLatLng(newPoint);
	return distance = latLngPoint.distanceFrom(newlatLngPoint)/10;
}
GMC.prototype.checkForHiddenMarkers = function(marker) {
	var a = [];
	if(this.gmarkers.length > 1) {
		var currentMarkerBounds = this.obscuringPixelDiv(marker);
		var otherMarkerBounds;
		for (var i = 0; i < this.gmarkers.length; i++) {
			if (this.gmarkers[i].markerName != marker.markerName) {
				if (!this.gmarkers[i].isHidden()) {
					if(this.gmarkers[i].type == "marker") {
						if (this.gmarkers[i].getPoint().lat() >= marker.getPoint().lat()) {
							otherMarkerBounds = this.obscuringPixelDiv(this.gmarkers[i]);
							if(this.GBoundIntersection(currentMarkerBounds, otherMarkerBounds)) {
								a.push (i);
							}
						}
					}
				}
			}
		}
	}
	return a;
}
GMC.prototype.GBoundIntersection = function (GA, GB) {
	if((GA[0] >= GB[0] && GA[0] <= GB[2]) || (GA[2] >= GB[0] && GA[2] <= GB[2]) || (GA[0] <= GB[0] && GA[2] >= GB[2])){
		if((GA[1] >= GB[1] && GA[1] <= GB[3]) || (GA[3] >= GB[1] && GA[3] <= GB[3]) || (GA[1] <= GB[1] && GA[3] >= GB[3])){
			return true;
		}
	}
	return false;
}
GMC.prototype.obscuringPixelDiv = function (marker) {
	//get pixel point from LatLng
	var icon = marker.getIcon();
	var PointpixelCoordinates = map.fromLatLngToDivPixel(marker.getPoint());
	//get NW offset in pixels from LatLng Px-Ax
	var NWx = PointpixelCoordinates.x - icon.iconAnchor.x;
	var NWy = PointpixelCoordinates.y - icon.iconAnchor.y;
	//get SE offset in pixels from LatLng
	var SEx = PointpixelCoordinates.x + (icon.iconSize.width - icon.iconAnchor.x);
	var SEy = PointpixelCoordinates.y;
	return a = new Array(NWx, NWy, SEx, SEy);
}
/* update server */
GMC.prototype.updateServerDone = function (v) {
	GMO.updateStatus(v);
	GMO.updateStatus("done");
}
/* address finder form */
GMC.prototype.findAddressForm = function () {
		var findAddressHtml = ''
			findAddressHtml = ''
			//+ '<form action="#">'
			+ ' <p class="findAddressHtml">'
			+ '  <input type="text" size="60" id="mapAddress" class="infoTabInputAddress" />' + this.opts.defaultAddressText
			+ '  <input type="button" value="find address" id="infoTabSubmitAddress" class="submitButton" onclick="findAddress(document.getElementById(\'mapAddress\').value); return false" />'
			+ ' </p>'
			//+ '</form>';
	return findAddressHtml
}
/* help */
GMC.prototype.helpHtml = function () {
	var string = ''
		+ '<ul>'
		+ '<li><b>keyboard tricks:</b><ul>'
		+ '<li>keys <i>up</i>, <i>down</i>, <i>left</i>, <i>right</i>: moves the map continuously while the key is pressed. Two keys can be pressed simultaneously in order to move diagonally.</li>'
		+ '<li>keys <i>+</i>, <i>-</i>: zoom in and out respectively</li>'
		+ '</ul></li>'
		+ '<li><b>mouse tricks:</b><ul>'
		+ '<li><i>click on a marker</i> to find out more about the location'
		+ '<li><i>click</i> anywhere else on the map to zoom-in on that particular point</li>'
		+ '<li><i>click-and-hold-down</i> anywhere on the map to drag in any direction</li>';
	if(this.opts.addPointsToMap) {
		string += ''
		+ '<li><i>right-mouse-click</i> on the map to add a marker (for example for creating a route)</li>'
		+ '<li><i>right-mouse-click</i> on a marker to delete it</li>'
	}
	else {
		string += ''
		+ '<li><i>right-mouse-click</i> to zoom out</li>'
	}
	string += ''
		+ '</ul></li>';
	if(this.opts.addKmlLink) {
		string += ''
		+ '<li><b>KML files</b> are for use in <a href="http://earth.google.com">Google Earth</a>.';
		+ 'Download KML files, save them to the desktop and open them.  From there, the should automatically start Google Earth.</li>';
	}
	if(this.opts.addDirections) {
		string += ''
		+ '<li><b>Finding directions</b> can be done by clicking on a first marker and choosing <i>from here</i> under the <i>directions</i> tab, then doing the same for another marker choosing the <i>to here</i> link. Finally, click on the <i>calculate route</i> link at the bottom of the <i>directions</i> tab and the map will calculate your route.</li>';
	}
	string += ''
		+ '<li>To <b>hide</b> help from the map, just click on the (x).</li>'
		+ '</ul>'
	return string;
}
GMC.prototype.viewPortWidth = function () {
	var viewportwidth;
	// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
	if (typeof window.innerWidth != 'undefined') {
		viewportwidth = window.innerWidth;
	}
	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
	else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
		viewportwidth = document.documentElement.clientWidth;
	}
	else {
			viewportwidth = document.getElementsByTagName('body')[0].clientHeight;
	}
	return viewportwidth;
}

GMC.prototype.viewPortHeight = function () {
	var viewportheight;
	// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerHeight and window.innerHeight
	if (typeof window.innerHeight != 'undefined') {
		viewportheight = window.innerHeight;
	}
	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
	else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientHeight != 'undefined' && document.documentElement.clientHeight != 0) {
		viewportheight = document.documentElement.clientHeight;
	}
	else {
			viewportheight = document.getElementsByTagName('body')[0].clientHeight;
	}
	return viewportheight;
}


/* additional functions */
Array.prototype.inArray = function (v) {
	for (var i=0; i < this.length; i++) {
		if (this[i] === v) {
			return i+1;
		}
	}
	return false;
}

Array.prototype.inSubArray = function(variableName, v) {
	var a = []
	for (var i=0; i < this.length; i++) {
		var x = this[i]
		a.push (x[variableName]);
	}
	return a.inArray(v);
}


function mapFunctionIsDefined(variable) {
	return (typeof(window[variable]) == "undefined")?  false: true;
}


function strpos( haystack, needle, offset){
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *     example 1: strpos('Kevin van Zonneveld', 'e', 5);
	// *     returns 1: 14
	var i = haystack.indexOf( needle, offset ); // returns -1
	return i >= 0 ? i : false;
}