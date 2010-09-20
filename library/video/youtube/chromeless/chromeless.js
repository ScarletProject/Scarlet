// This function is automatically called by the player once it loads
function onYouTubePlayerReady(playerId) {
	ytplayer = document.getElementById("ytPlayer");
	// This causes the updatePlayerInfo function to be called every 250ms to
	// get fresh data from the player
	// setInterval(updatePlayerInfo, 250);
	// updatePlayerInfo();
	// ytplayer.addEventListener("onStateChange", "onPlayerStateChange");
	// ytplayer.addEventListener("onError", "onPlayerError");
	//Load an initial video into the player

	// ytplayer.addEventListener('onClick', "onPlayerClick");
	ytplayer.cueVideoById("$videoid");
}

