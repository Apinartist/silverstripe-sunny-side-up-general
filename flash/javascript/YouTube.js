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
		setPlayer: function(player) {YouTube.player = player; alert(YouTube.player)},
		getPlayer: function() {
			if(YouTube.player) {
				return YouTube.player;
			}
			else {
				return document.getElementById(YouTube.elementID);
			}
		},

	loadVideo: function(videoID, width, height) {
		var params = { allowScriptAccess: "always" };
		var atts = { id: YouTube.elementID};
		swfobject.embedSWF("http://www.youtube.com/v/" + videoID + "?enablejsapi=1&playerapiid="+YouTube.elementID+ "",YouTube.elementID, width, height, "8", null, null, params, atts);
	},

	play: function() {
		YouTube.getPlayer().playVideo();
	},

	loadNew: function(videoID) {
		var player = YouTube.getPlayer();
		player.cueVideoById(videoID);
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
	},

	onYouTubePlayerReady: function(x) {
		alert("y");
	}

}


// This function is automatically called by the player once it loads
function onYouTubePlayerReady(playerId) {
	alert("go");
	var ytplayer = document.getElementById(playerId);
	//YouTube.setPlayer(ytplayer);
	ytplayer.addEventListener("onError", YouTube.onPlayerError);
	ytplayer.loadVideoById('Q9VZEhyaz');
	alert("done");
}



