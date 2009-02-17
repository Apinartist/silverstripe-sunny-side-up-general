<?php



/*
 *
 * This module allows you to add flashObjects the "correct" way.
 * You can either use a DataObjectDecorator OR just use pre-configured data for your flashobjects
 *
 *
 * @source http://code.google.com/p/swfobject/wiki/documentation
 * @see http://www.swffix.org/swfobject/generator/
 *

*/

	//may set
	//DataObject::add_extension('SiteTree', 'FlashObjectDOD');

	//must set
	FlashObject::setDefaultID("FlashObject");

	//basics
	FlashObject::setDefaultExternalFlashFile("");
	FlashObject::setDefaultWidth(826);
	FlashObject::setDefaultHeight(173);
	FlashObject::setDefaultFlashVersion("6.0.0");
	FlashObject::setDefaultAlternativeContent('<a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>');

	//parameters
	//FlashObject::addParam("quality", "high");
	//FlashObject::addParam("play", "");
	//FlashObject::addParam("loop", "");
	//FlashObject::addParam("menu", "");
	//FlashObject::addParam("quality", "");
	//FlashObject::addParam("scale", "");
	//FlashObject::addParam("salign", "");
	FlashObject::addParam("wmode", "transparent");
	//FlashObject::addParam("bgcolor", "");
	//FlashObject::addParam("base", "");
	//FlashObject::addParam("swliveconnect", "");
	//FlashObject::addParam("flashvars", "");
	//FlashObject::addParam("devicefont", "");
	//FlashObject::addParam("allowscriptaccess", "");
	//FlashObject::addParam("seamlesstabbing", "");
	//FlashObject::addParam("allowfullscreen", "");
	//FlashObject::addParam("allownetworking", "");


/*
How can you use HTML to configure your Flash content?

You can add the following often-used optional attributes [ http://www.w3schools.com/tags/tag_object.asp ] to the object element:

    * id
    * name
    * class
    * align

You can use the following optional Flash specific param elements [ http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_12701 ]:

    * play
    * loop
    * menu
    * quality
    * scale
    * salign
    * wmode
    * bgcolor
    * base
    * swliveconnect
    * flashvars
    * devicefont [ http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_13331 ]
    * allowscriptaccess [ http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_16494 ] and [ http://www.adobe.com/go/kb402975 ]
    * seamlesstabbing [ http://www.adobe.com/support/documentation/en/flashplayer/7/releasenotes.html ]
    * allowfullscreen [ http://www.adobe.com/devnet/flashplayer/articles/full_screen_mode.html ]
    * allownetworking [ http://livedocs.adobe.com/flash/9.0/main/00001079.html ]
*/