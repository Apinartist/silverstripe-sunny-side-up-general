/*
@author nicolaas[at]sunnysideup.co.nz
also see:
http://code.google.com/apis/youtube/player_parameters.html
http://code.google.com/apis/youtube/js_api_reference.html
*/

var YouTube = {

	elementID: "YouTubber",
		setElementID: function(v){YouTube.elementID = v;},

	player: null,
		setPlayer: function(player) {YouTube.player = player; },
		getPlayer: function() {
			if(YouTube.player) {
				return YouTube.player;
			}
			else {
				return document.getElementById(YouTube.elementID);
			}
		},

	loadVideo: function(videoID, width, height) {
		var params = { allowScriptAccess: "always", wmode: "transparent" };
		var atts = { id: YouTube.elementID};
		swfobject.embedSWF("http://www.youtube.com/apiplayer?version=3&enablejsapi=1&playerapiid="+YouTube.elementID,YouTube.elementID, width, height, "8", null, null, params, atts);
	},

	play: function() {
		YouTube.getPlayer().playVideo();
	},

	loadNew: function(videoID) {
		var player = YouTube.getPlayer();
		player.cueVideoById(videoID);
		player.playVideo();
		return false;
	},

	resize: function(width, height) {
		width = parseInt(width);
		height: parseInt(height);
		YouTube.getPlayer().setSize(width, height);
	},


	loadFullScreenVideo: function(videoID) {
		var x = 0;
		if (self.innerHeight) {
			x = self.innerWidth;
		}
		else if (document.documentElement && document.documentElement.clientHeight){
			x = document.documentElement.clientWidth;
		}
		else if (document.body) {
			x = document.body.clientWidth;
		}
		var y = 0;
		if (self.innerHeight){
			y = self.innerHeight;
		}
		else if (document.documentElement && document.documentElement.clientHeight){
			y = document.documentElement.clientHeight;
		}
		else if (document.body){
			y = document.body.clientHeight;
		}
		return YouTube.loadVideo(videoID, x, y);
	},

	onPlayerError: function(errorCode) {
		alert("An error occured of type:" + errorCode);
	}


}


// This function is automatically called by the player once it loads
function onYouTubePlayerReady(playerId) {
	var ytplayer = document.getElementById(playerId);
	YouTube.setPlayer(ytplayer);
	ytplayer.addEventListener("onError", YouTube.onPlayerError);
}



/*
 * Youtube Chromeless Video Plugin
 * http://www.viget.com/
 *
 * Copyright (c) 2010 Trevor Davis
 * Dual licensed under the MIT and GPL licenses.
 * Uses the same license as jQuery, see:
 * http://jquery.org/license
 *
 * @version 0.2
 */

(function($) {
  $.fn.ytplayerlink = function(ytVideoId, options){

    //Initial configuration
    var config = {
      ytVideoId  : ytVideoId,
      videoWidth  : '640',
      videoHeight : '360',
      videoIdBase : 'ytplayer',
      params : {
		    allowScriptAccess: 'always',
		    wmode: 'transparent'
		  }
    };

    return this.each(function(i) {


      // initial var setup

        var options    = $.extend(config, options),

            // set jQuery objects
            $itemToReplace      = $(this),

            // set variables
            videoId    = $itemToReplace.attr('id') || options.videoIdBase + i,

            // new DOM elements
            $video     = $itemToReplace.wrap( '<div class="video-player"></div>' ).parent(),
            $controls  = $('<div class="video-controls"></div>' ).appendTo( $video ),
            $toReplace = $('<div class="video"></div>').prependTo( $video ).attr('id', videoId),
            $bar,
            $indicator,
            $loaded,
            $mute,
            $play,
            $seek,

            // set up the special player object
            player;

        // bind public methods upfront
        $video.bind({

          // playing, pausing, muting,
          'togglePlay' : function(){ $video.togglePlay(); },
          'play'       : function(){ $video.play(); },
          'pause'      : function(){ $video.pause(); },
          'toggleMute' : function(){ $video.toggleMute(); },
          'mute'       : function(){ $video.mute(); },
          'unMute'     : function(){ $video.unMute(); },
          'seek'       : function(){ $video.seek(); },

          // initializing and revising the player
          'update'     : function(){ $video.update(); },
          'cue'        : function(){ player.cueVideoById( ytVideoId ); }

        });


      // control methods

        // function fired when the play/pause button is hit
        $video.togglePlay = function() {
          if( $play.hasClass('playing') ) {
            $video.trigger('pause');
          } else {
            $video.trigger('play');
          }
          return false;
        };

        // play the video
        $video.play = function() {
          player.playVideo();
          $play.removeClass('paused').addClass('playing').attr('title','Pause');
        };

        // pause
        $video.pause = function() {
          player.pauseVideo();
          $play.removeClass('playing').addClass('paused').attr('title','Play');
        };

        // function fired when the mute/unmute button is hit
        $video.toggleMute = function() {
          if( $mute.hasClass('muted') ) {
            $video.trigger('unMute');
          } else {
            $video.trigger('mute');
          }
          return false;
        };

        // mute the video
        $video.mute = function() {
          player.mute();
          $mute.addClass('muted').attr('title','Un-Mute');
        };

        // unmute
        $video.unMute = function() {
          player.unMute();
          $mute.removeClass('muted').attr('title','Mute');
        };

        //Seek to a position in the video
    		$video.seek = function(seekPosition) {
          var seekToPosition = Math.round(player.getDuration() * seekPosition);
          player.seekTo(seekToPosition, false);
        };



      // player init and update methods

        //Update the video status
    		$video.update = function() {

    		  if( player && player.getDuration ) {

            if( player.getPlayerState() === 1 ) {
              $video.play();
            } else if ( player.getPlayerState() === 0 ) {
              $video.pause();
            }

            if( player.getVideoBytesLoaded() > -1) {
              var loadedAmount = ( player.getVideoBytesLoaded() / player.getVideoBytesTotal())  * 100;
              $loaded.css( 'width', loadedAmount + '%' );
            }

            if( player.getCurrentTime() > 0 ) {
              var videoPosition = ( player.getCurrentTime() / player.getDuration() ) * 100;
              $indicator.css( 'left', videoPosition + '%' );
            }

    		  }

    		};


  			// the youtube movie calls this method when it loads
  			// DO NOT CHANGE THIS METHOD'S NAME
    		onYouTubePlayerReady = function( videoId ) {

    		  var $videoRef = $( document.getElementById( videoId ) ).parent();

    		  setInterval(function(){
    		    $videoRef.trigger('update');
    		  }, 250);

          $videoRef.trigger('cue');

        };



      // init methods

        // the embed!
    		$video.init = function() {

    		  swfobject.embedSWF(
            'http://www.youtube.com/apiplayer?&enablejsapi=1&playerapiid=' + videoId,
            videoId,
            options.videoWidth,
            options.videoHeight,
            '8',
            null,
            null,
            options.params,
            { id: videoId },
            function(){
              player = document.getElementById( videoId );
            }
          );

          $video.addControls();

    		};

        // add controls
    		$video.addControls = function() {

    		  //Play and pause button
    		  $play = $('<a/>', {
    		            href: '#',
            		    'class': 'play-pause',
            		    text: 'Play/Pause',
            		    title: 'Play',
            		    click: function() {
            		      $video.trigger('togglePlay');
            		      return false;
            		    }
            		  }).appendTo( $controls );

    		  //Play and pause button
    		  $mute = $('<a/>', {
    		            href: '#',
            		    'class': 'volume',
            		    text: 'Volume',
            		    title: 'Mute',
            		    click: function() {
            		      $video.trigger('toggleMute');
            		      return false;
            		    }
            		  }).appendTo( $controls );


  		    //Play and pause button
          $seek = $('<div/>', {
            		    'class': 'status',
            		    click: function(e) {
                      var skipTo      = e.pageX - $seek.offset().left,
                          statusWidth = $seek.width();
                      $video.seek( skipTo / statusWidth );
            		    }
            		  }).appendTo( $controls );

          $bar       = $('<div class="bar"></div>').appendTo($seek);
          $loaded    = $('<div class="loaded"></div>').appendTo($bar);
          $indicator = $('<span class="indicator"></span>').appendTo($bar);

        };


        $video.loadNew = function(ytVideoId) {
          player.cueVideoById(ytVideoId);
          player.playVideo();
          return false;
        },

        $video.resize = function(width, height) {
          width = parseInt(width);
          height: parseInt(height);
          player.setSize(width, height);
        },

        $video.loadFullScreenVideo = function(ytVideoId) {
          var x = 0;
          if (self.innerHeight) {
            x = self.innerWidth;
          }
          else if (document.documentElement && document.documentElement.clientHeight){
            x = document.documentElement.clientWidth;
          }
          else if (document.body) {
            x = document.body.clientWidth;
          }
          var y = 0;
          if (self.innerHeight){
            y = self.innerHeight;
          }
          else if (document.documentElement && document.documentElement.clientHeight){
            y = document.documentElement.clientHeight;
          }
          else if (document.body){
            y = document.body.clientHeight;
          }
          $video.resize(x,y)
          return $video.loadNew(ytVideoId);
        },

        onPlayerError = function(errorCode) {
          alert("An error occured of type:" + errorCode);
        }

        $video.init();

    });

  };

})(jQuery);
