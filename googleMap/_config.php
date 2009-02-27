<?php
/**
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 *
 * How to work this module
 * 1. review settings below
 * 2. add extension: DataObject::add_extension('SiteTree', 'GoogleMapLocationsDOD'));
 * 3. run db/build/?flush=1
 * you can add maps as follows by adding the following to your init() function in a page controller:
 * $this->addMap("showPagePointsMapXML"); //SEE GoogleMapLocationsDOD for the type of maps you can add
 *
 * for each page type you must / can:
 * - static $defaults = array ("HasGeoInfo" => 1); - allow points to be added in the CMS
 * - add a map and static map icon (to be implemented)
 *
 * you can filter the map data for particular pagetypes
 *
 * @ TO DO:
 * - lines
 * - polygons
 * - integrate with GIS model
 * - NO STATIC option
 * - NO dynamic option
*/


define("GoogleMapAPIKey", "abcetc...");
/* MAP*/
GoogleMap::setDefaultLatitude(12.0001);
GoogleMap::setDefaultLongitude(133.2210);
GoogleMap::setDefaultZoom(2);
GoogleMap::setGoogleMapWidth(473); //map width in pixels (ranges from around 100 to 900)
GoogleMap::setGoogleMapHeight(525); //map height in pixels (ranges from around 100 to 600)
GoogleMap::setMapTypeDefaultZeroToTwo(3); //"0" => "normal", "1" => "satellite", "2" => "satellite with markings", "3" => "natural"
GoogleMap::setViewFinderSize(200); //size of the view finder in pixels (e.g. 250)
GoogleMap::setMapAddTypeControl(true);//Allow the visitor to change the map type (e.g. from a satellite to a normal map)
GoogleMap::setMapControlSizeOneToThree(3);//map controller size (allows user to zoom and pan)", array("1" => "small", "2" => "medium", "3" => "large"), $value = "3"));
GoogleMap::setMapScaleInfoSizeInPixels(150); //size of the map scale in pixels (default is 100)
GoogleMap::setShowStaticMapFirst(0); //if set to 1, the map will load as a picture rather than an interactive map, with the opportunity to start an interactive map

/* STATIC MAP SETTINGS */
//center=-41.2943,173.2210&amp;zoom=5&amp;size=512x512&amp;maptype=roadmap - ONLY MAPTYPE IS REQUIRED
//max size is 512pixels
GoogleMap::setStaticMapSettings("maptype=terrain"); //

/* INCLUSIONS */
GoogleMap::setNoStatusAtAll(false); //hide map status (which shows information like ... loading new points now ...)
GoogleMap::setHiddenLayersRemovedFromList(false); //remove points hidden by visitors to your map
GoogleMap::setAddAntipodean(false); //add antipodean option (allowing visitors to find the exact opposite point on earth)
GoogleMap::setChangePageTitle(false); //adjust the page title when you change the map
GoogleMap::setAddDirections(false); //add directions finder to map pop-up windows
GoogleMap::setAddAddressFinder(false); //provide an address finder helping visitors to enter an address and search for it on the map
GoogleMap::setAddPointsToMap(false); //allow user to add their own points to the map using right-mouse-clicks

/* POLYS */
GoogleMap::setLineColour("#dcb916"); //colour for additional lines (e.g. routes) on map (use web colour codes)
GoogleMap::setLineWidth(5); //width of the line in pixels
GoogleMap::setLineOpacity(0.5); //opacity for the line (default is 0.5 - should range from transparent: 0 to opaque: 1
GoogleMap::setFillColour("#dcb916");//colour for polygons (e.g. regions) on map
GoogleMap::setFillOpacity(0.3);//opacity for polygons (default is 0.3 - should range from transparent: 0 to opaque: 1)
GoogleMap::setPolyIcon(""); //location for icon used for polygon and polyline (e.g. http://www.mysite.com/icon.png)

/* HELPDIVS */
GoogleMap::setSideBarDivId("GmapDropSideBarId"); //ID for DIV that shows additional information about map leave blank to remove)"
GoogleMap::setDropDownDivId(""); //ID for DIV of dropdown box with points in map (leave blank to remove)
GoogleMap::setTitleDivId("GmapTitleID"); //ID for DIV of map title (leave blank to remove)
GoogleMap::setLayerListDivId(""); //ID for DIV that shows list of map layers (leave blank to remove)
GoogleMap::setDirectionsDivId(""); //ID for DIV that shows directions from map (leave blank to remove)
GoogleMap::setStatusDivId(""); //ID for DIV that shows status of map

/* INFOWINDOW*/
GoogleMap::setInfoWindowOptions("{maxWidth:280, zoomLevel:17, mapType:G_HYBRID_MAP}"); //info window options (see http://code.google.com/apis/maps/documentation/reference.html for details)
GoogleMap::setAddCurrentAddressFinder(true); //add a tab with the address finder

/* MARKER AND ICONS (include title to have a title)*/
GoogleMap::setMarkerOptions("{draggable:false,bouncy:true,title: \"click me\"}"); //marker options (see http://code.google.com/apis/maps/documentation/reference.html for details)
GoogleMap::setPreloadImages(true); //pre-load marker images
GoogleMap::setDefaultIconUrl(""); //default Icon Url
GoogleMap::setIconFolder("googleMap/images/icons/"); //default Icon Folder - icons need to be name: i1, i2, i3, i4, etc...
GoogleMap::setIconWidth(20); //default icon width in pixels (e.g. 20)
GoogleMap::setIconHeight(34); //default icon height in pixels (e.g. 34)
GoogleMap::setIconExtension("png"); //default icon extension (e.g. png, gif or jpg)
GoogleMap::setIconMaxCount(12); //maximum number of layers, before reverting back to icon number one (e.g. 12)

/* SERVER INTERACTION */
GoogleMap::setLatFormFieldId(""); //latitude form field to be updated on new marker creation or marker movement
GoogleMap::setLngFormFieldId(""); //longitude form field to be updated on new marker creation or marker movement

/*  ADDRESS */
GoogleMap::setDefaultCountryCode(""); //default country code for address searches (to narrow searches to one country) - examples include US or NZ
GoogleMap::setDefaultAddressText(""); //extra phrase added to the end of an address (e.g. New Zealand or United Kingdom)
GoogleMap::setStyleSheetUrl("googleMap/css/mapDirections.css"); //style sheet to be used for formatting directions (e.g. googleMap/css/mapDirections.css)
GoogleMap::setLocaleForResults("en_NZ"); //language to be used for directions (e.g. en_US, fr, fr_CA, en_NZ, etc...

