<?php

/*
  All the settings are below.
	you can have multiple replacements listed here
*/

Sifr::replaceElement($named = "porcelain", $element = "h1", $font = "porcelain");
Sifr::setColoursAndCase($named = "porcelain", $sColor = "#000000", $sLinkColor = "#555555", $bBgColor = "#FFFFFF", $sHoverColor = "#999999", $sWmode = "transparent", $sCase = "");
Sifr::setPadding($named = "porcelain", $nPaddingTop = 0, $nPaddingBottom = 0, $nPaddingRight = 0, $nPaddingLeft = 0);
Sifr::setPosition($named = "porcelain", $textalign = "left", $offsetLeft = 0, $offsetTop = 0);



/*
 sIFR


 		This is the main method of sIFR. This is the method you need to call to replace
		your font headings with magicial flashness.

		@see http://wiki.novemberborn.net/sifr/How+to+use

		First Parameter  -
		 *  sSelector: This is the CSS selector you use to select the elements you want to replace.
			The supported CSS selectors are #, > and .. Whitespace is used to select descendants.
			Please use whitespace only for this, so instead of #foo > p use #foo>p.
			You can use multiple selectors by seperating them with a comma (",").

			Examples
				Sifr::replaceElement("#Content h2", "...
				Sifr::replaceElement("#Header h1.logo", "....

		--------------------------------------------------------------------------------

		Second Parameter -
			* The font you would like to use. At the moment the font must be in /sifr/fonts/ . It has to be lowercase and
			  no spaces.

				== HOW TO MAKE A FONT YOU CAN USE ==

				To export your new typeface, open the sifr.fla file (which is included with the download) in Flash Professional,
				and double-click the invisible textbox in the middle of the stage. If the "Properties" palette is not already
				visible, open it by selecting "Window > Properties", and select which font you'd like to use from the drop down
				menu. If you select a TrueType font, you can also create bold and italic styles for your font by clicking on the
				"I" or "B" buttons.

				To export the new file, choose "File > Export > Export Movie", and save as fontname.swf. Make sure to export as
				Flash 6!

				The standard sifr.fla file contains most of the English characters you will generally need. If you need to embed
				additional characters or languages, click the "Character" button, or the "Embed" button (for Flash 8) and select
				more characters from there and re-export.

				You can also try the sIFR Font Embedder for Windows or OpensIFR for Mac OS X (also works in Windows), which are
				applications to create the font files without requiring Flash.

				@see http://digitalretrograde.com/Projects/sifrFontEmbedder/
				@see http://ajaxian.com/archives/opensifr-tool-to-generate-font-files

				Once you have made your flash file drop it into /sifr/fonts/

		--------------------------------------------------------------------------------

		Third Parameter -

			sIFR takes thirtheen arguments, a lot of which are optional.

			The following arguments are what you can use to tweak and edit the replacement

		    * sFlashSrc: location of the Flash movie. You might need to use a relative (./movie.swf) or absolute (/movie.swf) here.
		    * sColor: Text color. All colors are in hex notation (#000000).
		    * sLinkColor: Text color for links.
		    * sHoverColor: Color for hovered links.
		    * sBgColor: Background color.
		    * nPaddingTop,
		      	nPaddingRight,
				nPaddingBottom,
				nPaddingLeft: if you use padding in the elements you want to replace, you have to
				set the amount of padding here (in pixels, but without the px part).

		    * sFlashVars: extra variables you want to pass on to the Flash. These variables are seperated by &. You can use:
		          o textalign=center: Center text horizontally
		          o offsetLeft=5: Pushes text 5px to the right. Of course you can use any number here.
		          o offsetTop=5: Pushes text 5px down.
		          o underline=true: Adds underline to links on hover

		    * sCase: Use upper to transform the text to upper-case, use lower to transform the text to lower-case. Depending on the browser
				this might give problems when you want to change the casing of special characters.

		    * sWmode: Set this argument to transparent if you want to use a transparent background. If you want to stack elements above the
				Flash movies, you need to set it to opaque. Mozilla browsers can have some difficulty rendering Flash movies with the sWmode set.
				Therefore setting this is not recommended.

		      Transparency is not supported in Opera 7.x, Safari < 1.2 & Flash 6, in Linux, and in very old (pre 1.0) Mozilla versions. In these
				browsers sIFR will fall back to the background color instead of using transparency.

			Examples
				Sifr::replaceElement("#Content h1", "tradegothic",
					sColor:"#000000",
					sLinkColor:"#000000",
					sBgColor:"#FFFFFF",
					sHoverColor:"#CCCCCC",
					nPaddingTop:20,
					nPaddingBottom:20,
					sFlashVars:"textalign=center&offsetTop=6");

				Sifr::replaceElement("#Sidebar h4.subTitle", "vandenkeere",
					sColor:"#ff0000",
					sLinkColor:"#000000",
					sBgColor:"#FFFFFF");


*/









